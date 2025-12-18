<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByToken(string $token): ?User
    {
        return User::where('token', $token)->first();
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return null;
        }

        // Check password using MD5 (legacy support)
        if (md5($password) === $user->password) {
            return $user;
        }

        // Also check with Hash for new passwords
        if (Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }

    public function findById(int $userId): ?User
    {
        return User::where('id_user', $userId)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
}
