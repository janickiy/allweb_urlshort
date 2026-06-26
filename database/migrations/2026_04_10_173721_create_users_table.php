<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the users table.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191);
            $table->string('api_token', 80)->nullable();
            $table->string('locale', 64)->nullable();
            $table->string('timezone', 64)->nullable();
            $table->rememberToken();
            $table->integer('role')->default(0);
            $table->timestamps();
            $table->string('stripe_id', 191)->nullable();
            $table->string('card_brand', 191)->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique('email', 'users_email_unique');
            $table->unique('api_token', 'users_api_token_unique');
        });
    }

    /**
     * Drop the users table.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
