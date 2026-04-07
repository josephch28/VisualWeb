<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\AuthService;

class AuthController
{
    private AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function login()
    {
        $username = $_POST['username'] ?? ($_POST['user'] ?? ($_POST['usuario'] ?? ''));
        $password = $_POST['password'] ?? ($_POST['contrasena'] ?? '');

        $result = $this->service->login($username, $password);

        if ($result['success']) {
            session_start();
            $_SESSION['user'] = $result['user'];
            $_SESSION['role'] = $result['role'];
            // To be retro-compatible with old views which might use 'usuario' and 'rol'
            $_SESSION['usuario'] = $result['user'];
            $_SESSION['rol'] = $result['role'];
        }

        echo json_encode($result);
    }

    public function logout()
    {
        session_start();
        session_destroy();
        echo json_encode(['success' => true]);
    }
}
