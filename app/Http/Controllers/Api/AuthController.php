<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'email' => $result['email'],
            'firstname' => $result['firstname'] ?? '',
            'lastname' => $result['lastname'] ?? '',
            'token' => $result['token'],
        ]);
    }
}
