<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // SQLite doesn't support modifying columns directly, so we need to:
        // 1. Create a new temporary column
        // 2. Copy data
        // 3. Drop old column
        // 4. Rename new column
        
        Schema::table('reactions', function (Blueprint $table) {
            if (Schema::hasColumn('reactions', 'type')) {
                // Add a new string column
                $table->string('type_new')->nullable();
            }
        });

        // Copy data from old type to new type
        DB::statement('UPDATE reactions SET type_new = type WHERE type IS NOT NULL');

        Schema::table('reactions', function (Blueprint $table) {
            // Drop the old enum column
            $table->dropColumn('type');
            // Rename the new column to 'type'
            $table->renameColumn('type_new', 'type');
        });
    }

    public function down()
    {
        // Revert: difficult with SQLite, but you can just drop and recreate
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->enum('type', ['like', 'love'])->nullable();
        });
    }
};
