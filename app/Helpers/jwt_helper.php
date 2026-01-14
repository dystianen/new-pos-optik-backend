<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('getJWTUser')) {
  function getJWTUser($required = true)
  {
    $request = \Config\Services::request();

    // Ambil Authorization header
    $authHeader = $request->getHeaderLine('Authorization');

    if (empty($authHeader)) {
      return $required ? null : (object)[];
    }

    // Extract token dari "Bearer <token>"
    $token = null;
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
      $token = $matches[1];
    }

    if (!$token) {
      return $required ? null : (object)[];
    }

    // Validate & decode JWT
    $decoded = validateJWT($token);

    if (!$decoded) {
      return $required ? null : (object)[];
    }

    // Optional: Filter hanya access token (bukan refresh token)
    if ($required && isset($decoded->type) && $decoded->type !== 'access') {
      return null;
    }

    return $decoded;
  }
}

if (!function_exists('generateJWT')) {
  function generateJWT($payload)
  {
    $key = getenv('JWT_SECRET_KEY') ?: 'your_secret_key';  // simpan di .env
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // 1 jam
    $payload = array_merge($payload, [
      'iat' => $issuedAt,
      'exp' => $expirationTime
    ]);

    return JWT::encode($payload, $key, 'HS256');
  }
}

if (!function_exists('validateJWT')) {
  function validateJWT($token)
  {
    try {
      $key = getenv('JWT_SECRET_KEY') ?: 'your_secret_key';
      return JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
      return false;
    }
  }
}
