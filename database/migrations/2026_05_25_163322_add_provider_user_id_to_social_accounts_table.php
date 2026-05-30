<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if the column already exists to avoid errors
        if (!Schema::hasColumn('social_accounts', 'provider_user_id')) {
            Schema::table('social_accounts', function (Blueprint $table) {
                $table->string('provider_user_id')->after('provider');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('social_accounts', 'provider_user_id')) {
            Schema::table('social_accounts', function (Blueprint $table) {
                $table->dropColumn('provider_user_id');
            });
        }
    }
};