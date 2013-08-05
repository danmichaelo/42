<?php

use Illuminate\Database\Migrations\Migration;

class AddObjectLocationColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('objects', function($table)
		{

			# call number as written, without "FA" prefix, e.g. "104/1"
			$table->string('location', 20);

			# deicaml sortable version, e.g. "104.1"
			$table->decimal('location_sort', 5, 2)
				  ->nullable()
				  ->unique();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('objects', function($table)
		{
			$table->dropColumn('location');
			$table->dropColumn('location_sort');
			$table->dropUnique('objects_location_sort_unique');
		});
	}

}