<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class R2Storage
{
  protected $s3Client;
  protected $bucketName;
  protected $publicUrlBase;

  public function __construct()
  {
    $this->bucketName = getenv('R2_BUCKET_NAME');
    $this->publicUrlBase = getenv('R2_PUBLIC_URL');

    $this->s3Client = new S3Client([
      'version'     => 'latest',
      'region'      => 'auto',
      'endpoint'    => getenv('R2_ENDPOINT_URL'),
      'credentials' => [
        'key'    => getenv('R2_ACCESS_KEY_ID'),
        'secret' => getenv('R2_SECRET_ACCESS_KEY'),
      ],
      'use_path_style_endpoint' => false,
    ]);
  }

  /**
   * Upload local file to R2 and return public URL (or false on failure)
   */
  public function uploadFile($filePath, $objectKey)
  {
    try {
      $this->s3Client->putObject([
        'Bucket'     => $this->bucketName,
        'Key'        => $objectKey,
        'SourceFile' => $filePath,
      ]);

      return rtrim($this->publicUrlBase, '/') . '/' . ltrim($objectKey, '/');
    } catch (AwsException $e) {
      log_message('error', 'R2 uploadFile error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Build public URL for an object key
   */
  public function getFileUrl($objectKey)
  {
    return rtrim($this->publicUrlBase, '/') . '/' . ltrim($objectKey, '/');
  }

  /**
   * Delete an object on R2.
   * Accepts either the object key (myfile.jpg) or a full URL previously stored in DB.
   * Returns true if object deleted or not found, false on error.
   */
  public function deleteFile(string $objectKeyOrUrl): bool
  {
    // Jika diberikan URL lengkap, ekstrak object key
    $objectKey = $this->extractObjectKey($objectKeyOrUrl);

    if (empty($objectKey)) {
      log_message('error', "R2 deleteFile: object key kosong untuk input: {$objectKeyOrUrl}");
      return false;
    }

    try {
      $this->s3Client->deleteObject([
        'Bucket' => $this->bucketName,
        'Key'    => $objectKey,
      ]);

      // optional: tunggu sampai terhapus (tidak wajib)
      // $this->s3Client->waitUntil('ObjectNotExists', ['Bucket' => $this->bucketName, 'Key' => $objectKey]);

      log_message('debug', "R2 deleteFile success: {$objectKey}");
      return true;
    } catch (AwsException $e) {
      // Jika error karena object tidak ditemukan, anggap sukses (idempotent)
      $code = $e->getAwsErrorCode();
      if (in_array($code, ['NoSuchKey', 'NotFound', '404'])) {
        log_message('debug', "R2 deleteFile: object not found (treated as success): {$objectKey}");
        return true;
      }

      log_message('error', "R2 deleteFile error for {$objectKey}: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Check if file exists on R2
   */
  public function fileExists(string $objectKeyOrUrl): bool
  {
    $objectKey = $this->extractObjectKey($objectKeyOrUrl);

    try {
      $this->s3Client->headObject([
        'Bucket' => $this->bucketName,
        'Key'    => $objectKey,
      ]);
      return true;
    } catch (AwsException $e) {
      $code = $e->getAwsErrorCode();
      if (in_array($code, ['NotFound', '404', 'NoSuchKey'])) {
        return false;
      }
      log_message('error', "R2 fileExists error for {$objectKey}: " . $e->getMessage());
      return false;
    }
  }

  /**
   * List files optionally by prefix. Returns array of keys (or false on error).
   */
  public function listFiles($prefix = null)
  {
    $params = [
      'Bucket' => $this->bucketName,
    ];
    if ($prefix !== null) {
      $params['Prefix'] = $prefix;
    }

    try {
      $result = $this->s3Client->listObjectsV2($params);
      $items = $result->get('Contents') ?: [];
      $keys = [];
      foreach ($items as $item) {
        $keys[] = $item['Key'];
      }
      return $keys;
    } catch (AwsException $e) {
      log_message('error', 'R2 listFiles error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Utility: extract object key from a full URL or return the input if it already looks like a key.
   */
  protected function extractObjectKey(string $objectKeyOrUrl): string
  {
    $input = trim($objectKeyOrUrl);

    // Jika input terlihat seperti URL (http/https), coba ekstrak bagian setelah base URL
    if (filter_var($input, FILTER_VALIDATE_URL)) {
      // Jika publicUrlBase diset dan URL diawali dengan base, hapus base
      if (!empty($this->publicUrlBase) && strpos($input, $this->publicUrlBase) === 0) {
        $key = substr($input, strlen(rtrim($this->publicUrlBase, '/')));
        $key = ltrim($key, '/');
        return $key;
      }

      // Jika tidak match base, ambil path terakhir sebagai fallback (tidak sempurna untuk nested paths)
      $parts = parse_url($input);
      if (isset($parts['path'])) {
        return ltrim($parts['path'], '/');
      }

      return '';
    }

    return $input;
  }
}
