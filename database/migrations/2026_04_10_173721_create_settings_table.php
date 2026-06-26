<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the settings table.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            return;
        }

        Schema::create('settings', function (Blueprint $table): void {
            $table->string('name', 128)->primary();
            $table->text('value')->nullable();
        });
    }

    /**
     * Drop the settings table.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
