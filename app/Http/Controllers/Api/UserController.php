<?php

namespace App\Http\Controllers\Api;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function profile(Request $request): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');

        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'Token required'], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        return response()->json([
            'id' => $user->id_user,
            'email' => $user->email,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'full_name' => $user->full_name,
        ]);
    }

    public function updateName(Request $request): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');

        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'Token required'], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
        ]);

        $user->update([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Name Updated',
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');

        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'Token required'], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6'],
            're_new_password' => ['required', 'string', 'same:new_password'],
        ]);

        // Check old password (MD5 for legacy support)
        $oldPassword = md5($request->input('old_password'));
        if ($user->password !== $oldPassword) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong Old Password',
            ], 400);
        }

        // Update password (MD5 for legacy compatibility)
        $user->update([
            'password' => md5($request->input('new_password')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password Updated',
        ]);
    }
}
