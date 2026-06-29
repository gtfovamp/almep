<?php

/*
|--------------------------------------------------------------------------
| REFERENCE SCHEMA — DOCUMENTATION ONLY
|--------------------------------------------------------------------------
| The tables ALREADY EXIST in the production database (phpMyAdmin).
| DO NOT run this migration against the live DB.
| It is provided only so a fresh/local clone can be created for testing.
| To use it locally:  php artisan migrate
| On production: leave the DB as-is.
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('admin_users')) {
            Schema::create('admin_users', function (Blueprint $t) {
                $t->increments('id');
                $t->text('username');
                $t->text('password_hash');
                $t->dateTime('created_at')->useCurrent();
                $t->dateTime('updated_at')->useCurrent();
            });
        }
        if (! Schema::hasTable('admin_sessions')) {
            Schema::create('admin_sessions', function (Blueprint $t) {
                $t->increments('id');
                $t->unsignedInteger('user_id');
                $t->text('token');
                $t->dateTime('expires_at');
                $t->dateTime('created_at')->useCurrent();
                $t->foreign('user_id')->references('id')->on('admin_users')->cascadeOnDelete();
            });
        }
        // NOTE: remaining tables (categories, subcategories, products, etc.)
        // are intentionally omitted here. Import the original SQL dump from
        // phpMyAdmin to recreate them locally if you need a full local copy.
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sessions');
        Schema::dropIfExists('admin_users');
    }
};
