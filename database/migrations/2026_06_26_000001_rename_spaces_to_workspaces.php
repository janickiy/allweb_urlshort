<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSpacesToWorkspaces extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('spaces') && ! Schema::hasTable('workspaces')) {
            Schema::rename('spaces', 'workspaces');
        }

        if (Schema::hasTable('links') && Schema::hasColumn('links', 'space_id') && ! Schema::hasColumn('links', 'workspace_id')) {
            Schema::table('links', function (Blueprint $table) {
                $table->renameColumn('space_id', 'workspace_id');
            });
        }

        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'option_spaces') && ! Schema::hasColumn('plans', 'option_workspaces')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->renameColumn('option_spaces', 'option_workspaces');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('workspaces') && ! Schema::hasTable('spaces')) {
            Schema::rename('workspaces', 'spaces');
        }

        if (Schema::hasTable('links') && Schema::hasColumn('links', 'workspace_id') && ! Schema::hasColumn('links', 'space_id')) {
            Schema::table('links', function (Blueprint $table) {
                $table->renameColumn('workspace_id', 'space_id');
            });
        }

        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'option_workspaces') && ! Schema::hasColumn('plans', 'option_spaces')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->renameColumn('option_workspaces', 'option_spaces');
            });
        }
    }
}
