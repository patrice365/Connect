<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$post = App\Models\Post::latest()->first();
if (!$post) {
    echo "No posts found.\n";
    exit(1);
}

echo json_encode($post->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;
