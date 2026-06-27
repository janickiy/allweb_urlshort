<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the domains table.
     */
    public function up(): void
    {
        if (Schema::hasTable('domains')) {
            return;
        }

        Schema::create('domains', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->string('name', 255);
            $table->string('index_page', 255)->nullable();
            $table->string('not_found_page', 255)->nullable();
            $table->integer('user_id');
            $table->timestamps();

            $table->index('name', 'domains_name_index');
            $table->index('user_id', 'domains_user_id_index');
        });
    }

    /**
     * Drop the domains table.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
