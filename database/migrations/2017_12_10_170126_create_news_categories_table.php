<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNewsCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name_ar');
			$table->string('name_en');
			$table->text('descriptin_ar', 65535);
			$table->text('descriptin_en', 65535);
			$table->string('icon');
			$table->integer('staff_id')->index('staff_id');
			$table->enum('status', array('active','in-active'))->default('active');
			$table->enum('type', array('merchant','user'))->default('user');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('news_categories');
	}

}
