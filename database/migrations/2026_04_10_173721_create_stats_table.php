<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the link click statistics table.
     */
    public function up(): void
    {
        if (Schema::hasTable('stats')) {
            return;
        }

        Schema::create('stats', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->integer('link_id');
            $table->integer('user_id');
            $table->string('referrer', 255)->nullable();
            $table->string('platform', 64)->nullable();
            $table->string('browser', 64)->nullable();
            $table->string('device', 64);
            $table->char('country', 2)->nullable();
            $table->char('language', 2)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('link_id', 'stats_link_id_index');
            $table->index('user_id', 'stats_user_id_index');
            $table->index('referrer', 'stats_referrer_index');
            $table->index('created_at', 'stats_created_at_index');
        });
    }

    /**
     * Drop the link click statistics table.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
