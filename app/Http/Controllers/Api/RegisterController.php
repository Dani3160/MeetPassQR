<?php

namespace App\Http\Controllers\Api;

use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthService $authService
    ) {}

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'repassword' => ['required', 'string', 'same:password'],
        ]);

        // Check if email already exists
        if ($this->userRepository->findByEmail($request->input('email'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email has been registered',
            ], 400);
        }

        // Generate token from email (MD5 hash)
        $token = md5($request->input('email'));

        // Create user with MD5 password (legacy support)
        $user = $this->userRepository->create([
            'email' => $request->input('email'),
            'password' => md5($request->input('password')), // MD5 for legacy compatibility
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'token' => $token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User Created',
            'email' => $user->email,
            'token' => $user->token,
        ], 201);
    }
}
