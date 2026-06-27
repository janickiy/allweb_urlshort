<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the links table.
     */
    public function up(): void
    {
        if (Schema::hasTable('links')) {
            return;
        }

        Schema::create('links', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->integer('user_id')->nullable();
            $table->string('alias', 255);
            $table->string('url', 2048);
            $table->string('title', 255)->nullable();
            $table->text('geo_target')->nullable();
            $table->text('platform_target')->nullable();
            $table->string('password', 191)->nullable();
            $table->tinyInteger('disabled')->default(0);
            $table->tinyInteger('public')->default(0);
            $table->string('expiration_url', 2048)->nullable();
            $table->integer('clicks')->nullable()->default(0);
            $table->integer('workspace_id')->nullable();
            $table->integer('domain_id')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'links_user_id_index');
            $table->index('alias', 'links_alias_index');
            $table->index('clicks', 'links_clicks_index');
            $table->index('workspace_id', 'links_workspace_id_index');
            $table->index('domain_id', 'links_domain_id_index');
        });
    }

    /**
     * Drop the links table.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
