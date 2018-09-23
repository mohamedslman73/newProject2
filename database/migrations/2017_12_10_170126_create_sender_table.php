<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSenderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sender', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->enum('type', array('sms','email'));
			$table->string('from_name');
			$table->string('from_email');
			$table->string('send_to');
			$table->string('subject')->nullable();
			$table->text('body', 65535);
			$table->string('file')->nullable();
			$table->integer('staff_id');
			$table->enum('status', array('request','success','failed'))->default('request');
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
		Schema::drop('sender');
	}

}
