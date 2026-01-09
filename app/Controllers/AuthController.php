<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    use ResponseTrait;
    protected $customerModel, $userModel, $roleModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        helper(['form', 'url']); // load form & URL helper
    }

    // =======================
    // API FUNCTIONS
    // =======================

    // GET /api/auth/register
    public function register()
    {
        $rules = [
            'customer_name' => 'required|min_length[3]|max_length[50]|is_unique[customers.customer_name]',
            'customer_email' => 'required|valid_email|is_unique[customers.customer_email]',
            'customer_password' => 'required|min_length[3]',
            'customer_phone' => 'required',
            'customer_dob' => 'required',
            'customer_gender' => 'required',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $firstErrorMessage = reset($errors); // Ambil error pertama

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $firstErrorMessage
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Simpan sebagai string JSON untuk database
        $data = [
            'customer_name' => $this->request->getVar('customer_name'),
            'customer_email' => $this->request->getVar('customer_email'),
            'customer_password' => password_hash($this->request->getVar('customer_password'), PASSWORD_DEFAULT),
            'customer_phone' => $this->request->getVar('customer_phone'),
            'customer_dob' => $this->request->getVar('customer_dob'),
            'customer_gender' => $this->request->getVar('customer_gender'),
        ];

        $this->customerModel->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Registered successfully'
        ]);
    }

    // GET /api/auth/login
    public function login()
    {
        $email = $this->request->getVar('customer_email');
        $password = $this->request->getVar('customer_password');

        $user = $this->customerModel->where('customer_email', $email)->first();

        if (is_null($user)) {
            return $this->respond([
                'status' => 401,
                'message' => 'Invalid username or password.'
            ], 401);
        }

        if (!password_verify($password, $user['customer_password'])) {
            return $this->respond([
                'status' => 401,
                'message' => 'Invalid username or password.'
            ], 401);
        }

        $key = getenv('JWT_SECRET_KEY');
        $iat = time();

        // Access Token - 1 jam (untuk transaksi sensitif)
        $accessTokenPayload = [
            "iss" => "Your Store",
            "iat" => $iat,
            "exp" => $iat + 3600, // 1 jam
            "user_id" => $user['customer_id'],
            "user_name" => $user['customer_name'],
            "email" => $user['customer_email'],
            "type" => "access"
        ];

        // Refresh Token - 30 hari (untuk kenyamanan)
        $refreshTokenPayload = [
            "iss" => "Your Store",
            "iat" => $iat,
            "exp" => $iat + (30 * 24 * 60 * 60), // 30 hari
            "user_id" => $user['customer_id'],
            "type" => "refresh"
        ];

        $accessToken = JWT::encode($accessTokenPayload, $key, 'HS256');
        $refreshToken = JWT::encode($refreshTokenPayload, $key, 'HS256');

        return $this->respond([
            'message' => 'Login successfully!',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'user' => [
                    'id' => $user['customer_id'],
                    'name' => $user['customer_name'],
                    'email' => $user['customer_email']
                ]
            ]
        ], 200);
    }

    // GET /api/auth/refresh
    public function refresh()
    {
        try {
            // Ambil refresh token dari request body
            $json = $this->request->getJSON();
            $refreshToken = $json->refresh_token ?? null;

            // Validasi: refresh token harus ada
            if (empty($refreshToken)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Refresh token tidak ditemukan'
                ], 400);
            }

            // Secret key (sama dengan yang dipakai saat generate token)
            $key = getenv('JWT_SECRET_KEY');

            if (empty($key)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'JWT secret key tidak ditemukan'
                ], 500);
            }

            // Decode dan validasi refresh token
            try {
                $decoded = JWT::decode($refreshToken, new Key($key, 'HS256'));
            } catch (Exception $e) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Refresh token tidak valid atau sudah expired',
                    'error' => $e->getMessage()
                ], 401);
            }

            // Validasi: pastikan ini adalah refresh token (bukan access token)
            if (!isset($decoded->type) || $decoded->type !== 'refresh') {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Token bukan refresh token'
                ], 400);
            }

            // Validasi: cek apakah user masih ada di database
            $customerModel = new \App\Models\CustomerModel();
            $user = $customerModel->find($decoded->user_id);

            if (!$user) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Generate access token baru
            $iat = time();
            $exp = $iat + 3600; // 1 jam

            $accessTokenPayload = [
                "iss" => "Issuer of the JWT",
                "aud" => "Audience that the JWT",
                "sub" => "Subject of the JWT",
                "iat" => $iat,
                "exp" => $exp,
                "user_id" => $user['customer_id'],
                "user_name" => $user['customer_name'],
                "email" => $user['customer_email'],
                "type" => "access"
            ];

            $newAccessToken = JWT::encode($accessTokenPayload, $key, 'HS256');

            // Return access token baru
            return $this->respond([
                'message' => 'Token berhasil diperbarui',
                'data' => [
                    'access_token' => $newAccessToken,
                    'token_type' => 'Bearer',
                    'expires_in' => 3600, // dalam detik
                    'user' => [
                        'id' => $user['customer_id'],
                        'name' => $user['customer_name'],
                        'email' => $user['customer_email']
                    ]
                ]
            ], 200);
        } catch (Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat refresh token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =======================
    // WEB DASHBOARD FUNCTIONS
    // =======================

    public function signin()
    {
        return view('auth/v_signin');
    }

    public function signinStore()
    {
        $session = session();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $data = $this->userModel->where('user_email', $email)->first();
        $role = $this->roleModel->where('role_id', $data['role_id'])->first();

        if ($data) {
            $pass = $data['password'];
            $authenticatePassword = password_verify($password, $pass);
            if ($authenticatePassword) {
                $ses_data = [
                    'id' => $data['user_id'],
                    'full_name' => $data['user_name'],
                    'email' => $data['user_email'],
                    'role_name' => $role['role_name'],
                    'isLoggedIn' => TRUE
                ];

                $session->set($ses_data);

                return redirect()->to(base_url('/dashboard'));
            } else {
                $session->setFlashdata('failed', 'Password is incorrect.');
                return redirect()->to('/signin');
            }
        } else {
            $session->setFlashdata('failed', 'Email does not exist.');
            return redirect()->to('/signin');
        }
    }

    public function logout()
    {
        session()->destroy();
        return view('auth/v_signin');
    }
}
