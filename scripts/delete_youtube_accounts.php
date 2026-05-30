<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SocialAccount;

$rows = SocialAccount::where('provider', 'youtube')->get();
if ($rows->isEmpty()) {
    echo "No YouTube social accounts found.\n";
    exit(0);
}

foreach ($rows as $r) {
    echo "Deleting social account id={$r->id} user_id={$r->user_id}\n";
    $r->delete();
}

echo "Done. YouTube social accounts deleted.\n";
