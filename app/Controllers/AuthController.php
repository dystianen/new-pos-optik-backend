<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;
    protected $customerModel, $userModel, $roleModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        helper(['form', 'url']);
    }

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

        if ($data) {
            $role = $this->roleModel->where('role_id', $data['role_id'])->first();
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
