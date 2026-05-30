<?php
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap the app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Facades are ready after bootstrap
Illuminate\Support\Facades\App::setFacadeApplication($app);

use Illuminate\Http\Request;

echo "Starting simulated comment POST...\n";

// Find first user
$userModel = new App\Models\User();
$user = $userModel->first();
if (! $user) {
    echo "No users found in DB. Create a user first.\n";
    exit(1);
}

// Log in the user
Illuminate\Support\Facades\Auth::login($user);

// Create request
$req = Request::create('/youtube/comment/test-video-id', 'POST', ['comment' => 'Simulated test comment']);

try {
    // If Google client library isn't installed, provide a minimal stub so controller can run.
    if (! class_exists('Google_Client')) {
        class Google_Client {
            protected $accessToken;
            public function setClientId($v) {}
            public function setClientSecret($v) {}
            public function setAccessToken($t) { $this->accessToken = $t; }
            public function setScopes($s) {}
            public function isAccessTokenExpired() { return false; }
            public function fetchAccessTokenWithRefreshToken($r) { $this->accessToken = ['access_token' => $r . '_refreshed']; return $this->accessToken; }
            public function getAccessToken() { return is_array($this->accessToken) ? $this->accessToken : ['access_token' => $this->accessToken]; }
        }
    }

    $controller = new App\Http\Controllers\PostController();
    $response = $controller->postYouTubeComment($req, 'test-video-id');
    // If it's a JsonResponse or RedirectResponse
    if (method_exists($response, 'getContent')) {
        echo "Response content:\n";
        echo $response->getContent() . "\n";
    } else {
        var_dump($response);
    }
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "Done.\n";
