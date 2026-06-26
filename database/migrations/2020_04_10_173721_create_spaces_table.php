<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpacesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
	{
		Schema::create('workspaces', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->index('workspaces_user_id_index');
			$table->string('name', 255)->index('workspaces_name_index');
			$table->integer('color')->nullable();
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
		Schema::drop('workspaces');
	}
}
