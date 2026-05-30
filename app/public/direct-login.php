<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Get or create a user
$user = User::first();
if (!$user) {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@connect.test',
        'username' => 'admin',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
}

Auth::login($user);
header('Location: /dashboard');
exit;
