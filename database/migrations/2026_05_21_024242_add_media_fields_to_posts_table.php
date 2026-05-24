<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('media_path')->nullable()->after('content');
            $table->enum('media_type', ['image', 'video'])->nullable()->after('media_path');
            $table->string('original_name')->nullable()->after('media_type');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['media_path', 'media_type', 'original_name']);
        });
    }
};