<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the subscription plans table.
     */
    public function up(): void
    {
        if (Schema::hasTable('plans')) {
            return;
        }

        Schema::create('plans', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->string('product', 255);
            $table->string('name', 255);
            $table->text('description');
            $table->integer('trial_days')->nullable();
            $table->string('currency', 12);
            $table->tinyInteger('decimals')->nullable();
            $table->string('plan_month', 255)->nullable();
            $table->string('plan_year', 255)->nullable();
            $table->integer('amount_month')->nullable();
            $table->integer('amount_year')->nullable();
            $table->tinyInteger('visibility')->nullable();
            $table->string('color', 32);
            $table->tinyInteger('option_api')->nullable();
            $table->integer('option_links')->nullable();
            $table->integer('option_workspaces')->nullable();
            $table->integer('option_domains')->nullable();
            $table->tinyInteger('option_stats')->nullable();
            $table->tinyInteger('option_geo')->nullable();
            $table->tinyInteger('option_platform')->nullable();
            $table->tinyInteger('option_expiration')->nullable();
            $table->tinyInteger('option_password')->nullable();
            $table->tinyInteger('option_disabled')->nullable();
            $table->tinyInteger('option_utm')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Drop the subscription plans table.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
