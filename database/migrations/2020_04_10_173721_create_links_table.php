<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
	{
		Schema::create('links', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->index('links_user_id_index');
			$table->string('alias', 255)->index('links_alias_index');
			$table->string('url', 2048);
			$table->string('title', 255)->nullable();
			$table->text('geo_target')->nullable();
			$table->text('platform_target')->nullable();
			$table->string('password')->nullable();
			$table->tinyInteger('disabled')->default(0);
			$table->tinyInteger('public')->default(0);
			$table->string('expiration_url', 2048)->nullable();
			$table->integer('clicks')->nullable()->default(0)->index('links_clicks_index');
			$table->integer('workspace_id')->nullable()->index('links_workspace_id_index');
			$table->integer('domain_id')->nullable()->index('links_domain_id_index');
			$table->timestamp('ends_at')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::drop('links');
	}
}
