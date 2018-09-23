<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('news_category_id')->index('news_category_id');
			$table->string('name_ar');
			$table->string('name_en');
			$table->text('content_ar', 65535);
			$table->text('content_en', 65535);
			$table->string('image');
			$table->integer('staff_id')->index('staff_id');
			$table->enum('status', array('active','in-active'))->default('active');
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
		Schema::drop('news');
	}

}
