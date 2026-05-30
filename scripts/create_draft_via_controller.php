<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as IlluminateUploadedFile;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PostController;
use App\Models\User;
use App\Models\Post;

$user = User::find(1);
if (!$user) {
    echo "User ID 1 not found.\n";
    exit(1);
}

Auth::login($user);

$videoPath = __DIR__ . '/test_small.mp4';
if (!file_exists($videoPath)) {
    // create a tiny placeholder mp4 file
    file_put_contents($videoPath, "\x00\x00\x00\x18ftypmp42\x00\x00\x00\x00mp42mp41");
}

$uploaded = new IlluminateUploadedFile($videoPath, 'test_small.mp4', 'video/mp4', null, true);

$req = Request::create('/posts', 'POST', [
    'platform' => 'youtube',
    'title' => 'Controller draft upload',
    'content' => 'Draft created via controller script',
    'action' => 'draft',
]);
$req->files->set('video_file', $uploaded);

$controller = new PostController();

try {
    $response = $controller->store($req);
    echo "Controller store executed.\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

$last = Post::latest()->first();
if ($last) {
    echo "Latest post ID: {$last->id}, status: {$last->status}, title: {$last->title}\n";
} else {
    echo "No posts found after controller run.\n";
}
