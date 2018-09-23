<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributeCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attribute_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('name_ar', 65535);
			$table->text('name_en', 65535);
			$table->text('description_ar', 65535);
			$table->text('description_en', 65535);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attribute_categories');
	}

}
