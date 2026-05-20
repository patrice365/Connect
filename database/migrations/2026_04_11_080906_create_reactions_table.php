<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('reactionable');
            $table->string('type'); // like, love, haha, etc.
            $table->timestamps();
            $table->unique(['user_id', 'reactionable_id', 'reactionable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reactions');
    }
};