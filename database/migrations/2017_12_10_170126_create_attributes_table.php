<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attributes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('attribute_category_id');
			$table->enum('type', array('file','text','radio','checkbox','select','textarea'));
			$table->text('name_ar', 65535);
			$table->text('name_en', 65535);
			$table->text('description_ar', 65535);
			$table->text('description_en', 65535);
			$table->enum('multi_lang', array('active','in-active'))->default('active');
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
		Schema::drop('attributes');
	}

}
