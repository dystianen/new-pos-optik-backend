<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthAPI implements FilterInterface
{
  /**
   * Do whatever processing this filter needs to do.
   * By default it should not return anything during
   * normal execution. However, when an abnormal state
   * is found, it should return an instance of
   * CodeIgniter\HTTP\Response. If it does, script
   * execution will end and that Response will be
   * sent back to the client, allowing for error pages,
   * redirects, etc.
   *
   * @param RequestInterface $request
   * @param array|null       $arguments
   *
   * @return RequestInterface|ResponseInterface|string|void
   */
  public function before(RequestInterface $request, $arguments = null)
  {
    // Skip filter untuk OPTIONS request (CORS preflight)
    if ($request->getMethod() === 'options') {
      return $request;
    }

    $response = service('response');

    // Ambil header Authorization
    $authHeader = $request->getHeaderLine('Authorization');

    // Cek apakah header Authorization ada
    if (empty($authHeader)) {
      return $response->setJSON([
        'status' => 'error',
        'message' => 'Token tidak ditemukan'
      ])->setStatusCode(401);
    }

    // Ekstrak token dari header (format: Bearer <token>)
    $token = null;
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
      $token = $matches[1];
    }

    if (empty($token)) {
      return $response->setJSON([
        'status' => 'error',
        'message' => 'Format token tidak valid'
      ])->setStatusCode(401);
    }

    try {
      // Secret key untuk JWT (sebaiknya simpan di .env)
      $key = getenv('JWT_SECRET_KEY') ?: 'your-secret-key-here';

      // Decode dan validasi token
      $decoded = JWT::decode($token, new Key($key, 'HS256'));

      // Simpan data user ke session atau attribute (hindari dynamic property)
      session()->set('user_data', $decoded);

      // Atau gunakan setAttribute jika tersedia di versi CI4 Anda
      // $request->setAttribute('user', $decoded);

      // Opsional: Cek expiration time
      if (isset($decoded->exp) && $decoded->exp < time()) {
        return $response->setJSON([
          'status' => 'error',
          'message' => 'Token sudah kadaluarsa'
        ])->setStatusCode(401);
      }
    } catch (Exception $e) {
      return $response->setJSON([
        'status' => 'error',
        'message' => 'Token tidak valid: ' . $e->getMessage()
      ])->setStatusCode(401);
    }

    // Token valid, lanjutkan request
    return $request;
  }

  /**
   * Allows After filters to inspect and modify the response
   * object as needed. This method does not allow any way
   * to stop execution of other after filters, short of
   * throwing an Exception or Error.
   *
   * @param RequestInterface  $request
   * @param ResponseInterface $response
   * @param array|null        $arguments
   *
   * @return ResponseInterface|void
   */
  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Tidak perlu action setelah response
  }
}
