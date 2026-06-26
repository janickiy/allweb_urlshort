<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the subscription items table.
     */
    public function up(): void
    {
        if (Schema::hasTable('subscription_items')) {
            return;
        }

        Schema::create('subscription_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->string('stripe_id', 191);
            $table->string('stripe_plan', 191);
            $table->integer('quantity');
            $table->timestamps();

            $table->unique(['subscription_id', 'stripe_plan'], 'subscription_items_subscription_id_stripe_plan_unique');
            $table->index('stripe_id', 'subscription_items_stripe_id_index');
        });
    }

    /**
     * Drop the subscription items table.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};
