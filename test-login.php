<?php
/**
 * Test Login Script
 * 
 * Usage: php test-login.php <email> <password>
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = $argv[1] ?? 'daniramdani121201@gmail.com';
$password = $argv[2] ?? 'test123';

echo "Testing login for: {$email}\n";
echo "Password: {$password}\n";
echo "MD5 Hash: " . md5($password) . "\n";
echo "---\n";

$user = App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->email}\n";
echo "Stored password hash: {$user->password}\n";
echo "---\n";

// Test MD5
$md5Match = md5($password) === $user->password;
echo "MD5 Match: " . ($md5Match ? "✅ YES" : "❌ NO") . "\n";

if ($md5Match) {
    echo "\n✅ Password is correct! Login should work.\n";
} else {
    echo "\n❌ Password mismatch!\n";
    echo "\nTo reset password, run:\n";
    echo "mysql -u root -padmin123 qrinvite_database -e \"UPDATE users SET password = MD5('{$password}') WHERE email = '{$email}';\"\n";
}
