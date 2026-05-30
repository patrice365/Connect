<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

$titles = [
    'Programmatic test draft',
    'Automated test draft',
    'Controller draft upload',
];

$deleted = 0;
foreach ($titles as $t) {
    $posts = Post::where('title', $t)->get();
    foreach ($posts as $p) {
        // delete associated files if present
        if ($p->video_path) {
            Storage::disk('public')->delete($p->video_path);
        }
        if ($p->thumbnail_path) {
            Storage::disk('public')->delete($p->thumbnail_path);
        }
        $p->forceDelete();
        $deleted++;
        echo "Deleted post ID {$p->id} (title: {$t})\n";
    }
}

// remove local placeholder
$local = __DIR__ . '/test_small.mp4';
if (file_exists($local)) {
    unlink($local);
    echo "Removed placeholder file test_small.mp4\n";
}

echo "Cleanup complete. Deleted posts: {$deleted}\n";
