<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename the legacy spaces table to the current workspaces table.
     */
    public function up(): void
    {
        if (Schema::hasTable('spaces') && ! Schema::hasTable('workspaces')) {
            Schema::rename('spaces', 'workspaces');
        }
    }

    /**
     * Restore the legacy spaces table name.
     */
    public function down(): void
    {
        if (Schema::hasTable('workspaces') && ! Schema::hasTable('spaces')) {
            Schema::rename('workspaces', 'spaces');
        }
    }
};
