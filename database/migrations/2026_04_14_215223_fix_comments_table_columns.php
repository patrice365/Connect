<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // Rename body to content if body exists and content does not
            if (Schema::hasColumn('comments', 'body') && !Schema::hasColumn('comments', 'content')) {
                $table->renameColumn('body', 'content');
            }

            // Add parent_id if missing
            if (!Schema::hasColumn('comments', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('comments')->onDelete('cascade');
            }

            // Add edited_at if missing
            if (!Schema::hasColumn('comments', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->after('content');
            }
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'content') && !Schema::hasColumn('comments', 'body')) {
                $table->renameColumn('content', 'body');
            }
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'edited_at']);
        });
    }
};