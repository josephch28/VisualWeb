<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\IUserRepository;

class AuthService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $username, string $password): array
    {
        $user = $this->userRepository->findByCredentials($username, $password);

        if ($user) {
            return [
                'success' => true,
                'user' => $user->getUsername(),
                'role' => $user->getRole()
            ];
        }

        return [
            'success' => false,
            'errorMsg' => 'Invalid username or password.'
        ];
    }
}
