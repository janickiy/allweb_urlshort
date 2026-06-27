<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the languages table.
     */
    public function up(): void
    {
        if (Schema::hasTable('languages')) {
            return;
        }

        Schema::create('languages', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->string('code', 64);
            $table->string('name', 255);
            $table->string('dir', 32);
            $table->tinyInteger('default')->nullable()->default(0);

            $table->unique('code', 'languages_code_unique');
        });
    }

    /**
     * Drop the languages table.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
