<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    /**
     * We will add the required database tables as required.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'external_auth_id')) {
            // Only add the column if it does not already exist
            Schema::table('users', static function (Blueprint $table) {
                $table->string('external_auth_id')->nullable()->after('id');
            });
        }
        
        if (!Schema::hasColumn('users', 'email_confirmed')) {
            // Only add the column if it does not already exist
            Schema::table('users', static function (Blueprint $table) {
                $table->boolean('email_confirmed')->default(true)->nullable()->after('email');
            });
        }
        
        if (!Schema::hasColumn('roles', 'external_auth_id')) {
            // Only add the column if it does not already exist
            Schema::table('roles', static function (Blueprint $table) {
                // Add the required azure_id for sake of tracking.
                $table->string('external_auth_id')->nullable()->after('id');
            });
        }
        
    }
};
