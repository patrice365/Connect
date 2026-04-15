<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();                           // Auto-incrementing primary key
            $table->foreignId('user_id')            // Link to users table
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('email')->index();       // Keep email for reference, indexed
            $table->string('token', 100);           // Token (long enough for security)
            $table->boolean('used')->default(false); // Mark if token has been used
            $table->timestamp('created_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Optional: token expiration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};