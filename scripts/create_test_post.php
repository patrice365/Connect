<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use App\Models\User;

$user = User::find(1);
if (!$user) {
    echo "User ID 1 not found.\n";
    exit(1);
}

$post = Post::create([
    'user_id'  => $user->id,
    'title'    => 'Programmatic test draft',
    'content'  => 'Created programmatically to verify posting flow.',
    'status'   => 'draft',
    'platform' => 'youtube',
]);

echo "Created post ID: " . ($post->id ?? 'n/a') . "\n";
