<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the password reset tokens table used by the application.
     */
    public function up(): void
    {
        if (Schema::hasTable('password_resets')) {
            return;
        }

        Schema::create('password_resets', function (Blueprint $table): void {
            $table->string('email', 191);
            $table->string('token', 191);
            $table->timestamp('created_at')->nullable();

            $table->index('email', 'password_resets_email_index');
        });
    }

    /**
     * Drop the password reset tokens table.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
