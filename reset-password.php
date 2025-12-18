<?php
/**
 * Reset Password Script
 * 
 * Usage: php reset-password.php <email> <new_password>
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

if ($argc < 3) {
    echo "Usage: php reset-password.php <email> <new_password>\n";
    exit(1);
}

$email = $argv[1];
$newPassword = $argv[2];

$user = App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found: {$email}\n";
    exit(1);
}

$oldPassword = $user->password;
$user->password = md5($newPassword);
$user->save();

echo "✅ Password reset successful!\n";
echo "Email: {$email}\n";
echo "Old hash: {$oldPassword}\n";
echo "New hash: " . md5($newPassword) . "\n";
echo "\nYou can now login with:\n";
echo "Email: {$email}\n";
echo "Password: {$newPassword}\n";
