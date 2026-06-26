<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the legacy spaces table before it is renamed to workspaces.
     */
    public function up(): void
    {
        if (Schema::hasTable('spaces') || Schema::hasTable('workspaces')) {
            return;
        }

        Schema::create('spaces', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->string('name', 255);
            $table->integer('color')->nullable();
            $table->timestamps();

            $table->index('user_id', 'user_id');
            $table->index('name', 'name');
        });
    }

    /**
     * Drop the legacy spaces table.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
