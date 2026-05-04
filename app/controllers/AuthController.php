<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function authenticateUser(string $email, string $password) {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    public function registerUser(string $name, string $email, string $password) {
        if ($this->userModel->findByEmail($email)) {
            return null;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $this->userModel->create($name, $email, $hashedPassword);
    }
}