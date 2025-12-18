<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->authenticate($email, $password);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        return [
            'success' => true,
            'email' => $user->email,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'token' => $user->token,
        ];
    }

    public function generateToken(): string
    {
        return Str::random(100);
    }
}
