<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateErrorLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('error_log', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('model_type', 150)->nullable();
			$table->integer('model_id')->nullable();
			$table->enum('type', array('error'))->default('error');
			$table->text('data', 65535);
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
		Schema::drop('error_log');
	}

}
