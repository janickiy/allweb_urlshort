<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move dependent-row cleanup to database-level foreign keys.
     */
    public function up(): void
    {
        $this->normalizeGuestLinks();
        $this->deleteRowsThatWouldBreakUnsignedColumns();
        $this->updateForeignKeyColumnTypes();
        $this->normalizeGuestStats();
        $this->deleteRowsThatWouldBreakForeignKeys();
        $this->addForeignKeys();
    }

    /**
     * Remove the added foreign keys and restore the legacy integer user columns.
     */
    public function down(): void
    {
        Schema::table('subscription_items', function (Blueprint $table): void {
            $table->dropForeign(['subscription_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        Schema::table('stats', function (Blueprint $table): void {
            $table->dropForeign(['link_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('links', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['workspace_id']);
            $table->dropForeign(['domain_id']);
        });

        Schema::table('domains', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        Schema::table('workspaces', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        $this->restoreLegacyGuestMarkers();

        Schema::table('links', function (Blueprint $table): void {
            $table->integer('user_id')->nullable()->change();
        });

        Schema::table('domains', function (Blueprint $table): void {
            $table->integer('user_id')->change();
        });

        Schema::table('workspaces', function (Blueprint $table): void {
            $table->integer('user_id')->change();
        });

        Schema::table('stats', function (Blueprint $table): void {
            $table->integer('user_id')->change();
        });
    }

    private function normalizeGuestLinks(): void
    {
        DB::table('links')
            ->where('user_id', '<=', 0)
            ->update(['user_id' => null]);
    }

    private function normalizeGuestStats(): void
    {
        DB::table('stats')
            ->where('user_id', 0)
            ->update(['user_id' => null]);
    }

    private function restoreLegacyGuestMarkers(): void
    {
        DB::table('links')
            ->whereNull('user_id')
            ->update(['user_id' => 0]);

        DB::table('stats')
            ->whereNull('user_id')
            ->update(['user_id' => 0]);
    }

    private function deleteRowsThatWouldBreakForeignKeys(): void
    {
        $this->deleteLinksWithMissingParents();
        $this->deleteDomainsWithMissingUsers();
        $this->deleteWorkspacesWithMissingUsers();
        $this->deleteLinksWithMissingParents();
        $this->deleteStatsWithMissingParents();
        $this->deleteSubscriptionsWithMissingUsers();
        $this->deleteSubscriptionItemsWithMissingSubscriptions();
    }

    private function deleteRowsThatWouldBreakUnsignedColumns(): void
    {
        DB::table('domains')
            ->where('user_id', '<=', 0)
            ->delete();

        DB::table('workspaces')
            ->where('user_id', '<=', 0)
            ->delete();

        DB::table('stats')
            ->where('user_id', '<', 0)
            ->delete();

        DB::table('subscriptions')
            ->where('user_id', '<=', 0)
            ->delete();
    }

    private function deleteLinksWithMissingParents(): void
    {
        DB::table('links')
            ->where(function (QueryBuilder $query): void {
                $query
                    ->where(function (QueryBuilder $query): void {
                        $query
                            ->whereNotNull('user_id')
                            ->whereNotExists(function (QueryBuilder $query): void {
                                $query
                                    ->selectRaw('1')
                                    ->from('users')
                                    ->whereColumn('users.id', 'links.user_id');
                            });
                    })
                    ->orWhere(function (QueryBuilder $query): void {
                        $query
                            ->whereNotNull('workspace_id')
                            ->whereNotExists(function (QueryBuilder $query): void {
                                $query
                                    ->selectRaw('1')
                                    ->from('workspaces')
                                    ->whereColumn('workspaces.id', 'links.workspace_id');
                            });
                    })
                    ->orWhere(function (QueryBuilder $query): void {
                        $query
                            ->whereNotNull('domain_id')
                            ->whereNotExists(function (QueryBuilder $query): void {
                                $query
                                    ->selectRaw('1')
                                    ->from('domains')
                                    ->whereColumn('domains.id', 'links.domain_id');
                            });
                    });
            })
            ->delete();
    }

    private function deleteDomainsWithMissingUsers(): void
    {
        DB::table('domains')
            ->where('user_id', '<=', 0)
            ->orWhereNotExists(function (QueryBuilder $query): void {
                $query
                    ->selectRaw('1')
                    ->from('users')
                    ->whereColumn('users.id', 'domains.user_id');
            })
            ->delete();
    }

    private function deleteWorkspacesWithMissingUsers(): void
    {
        DB::table('workspaces')
            ->where('user_id', '<=', 0)
            ->orWhereNotExists(function (QueryBuilder $query): void {
                $query
                    ->selectRaw('1')
                    ->from('users')
                    ->whereColumn('users.id', 'workspaces.user_id');
            })
            ->delete();
    }

    private function deleteStatsWithMissingParents(): void
    {
        DB::table('stats')
            ->where('user_id', '<', 0)
            ->orWhere(function (QueryBuilder $query): void {
                $query
                    ->whereNotNull('user_id')
                    ->whereNotExists(function (QueryBuilder $query): void {
                        $query
                            ->selectRaw('1')
                            ->from('users')
                            ->whereColumn('users.id', 'stats.user_id');
                    });
            })
            ->orWhereNotExists(function (QueryBuilder $query): void {
                $query
                    ->selectRaw('1')
                    ->from('links')
                    ->whereColumn('links.id', 'stats.link_id');
            })
            ->delete();
    }

    private function deleteSubscriptionsWithMissingUsers(): void
    {
        DB::table('subscriptions')
            ->whereNotExists(function (QueryBuilder $query): void {
                $query
                    ->selectRaw('1')
                    ->from('users')
                    ->whereColumn('users.id', 'subscriptions.user_id');
            })
            ->delete();
    }

    private function deleteSubscriptionItemsWithMissingSubscriptions(): void
    {
        DB::table('subscription_items')
            ->whereNotExists(function (QueryBuilder $query): void {
                $query
                    ->selectRaw('1')
                    ->from('subscriptions')
                    ->whereColumn('subscriptions.id', 'subscription_items.subscription_id');
            })
            ->delete();
    }

    private function updateForeignKeyColumnTypes(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('domains', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('workspaces', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('stats', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    private function addForeignKeys(): void
    {
        Schema::table('workspaces', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('domains', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('links', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('domain_id')->references('id')->on('domains')->cascadeOnDelete();
        });

        Schema::table('stats', function (Blueprint $table): void {
            $table->foreign('link_id')->references('id')->on('links')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('subscription_items', function (Blueprint $table): void {
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->cascadeOnDelete();
        });
    }
};
