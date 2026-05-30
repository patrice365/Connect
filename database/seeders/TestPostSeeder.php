<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class TestPostSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;

        $user = User::find($userId);
        if (!$user) {
            $this->command->info("User {$userId} not found. Skipping test post creation.");
            return;
        }

        Post::create([
            'user_id'  => $user->id,
            'title'    => 'Automated test draft',
            'content'  => 'This draft was created by TestPostSeeder to verify posting works.',
            'status'   => 'draft',
            'platform' => 'youtube',
        ]);

        $this->command->info('Test draft post created.');
    }
}
