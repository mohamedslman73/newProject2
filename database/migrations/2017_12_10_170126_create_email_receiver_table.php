<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailReceiverTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_receiver', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('email_id');
			$table->integer('receivermodel_id')->unsigned()->nullable();
			$table->string('receivermodel_type');
			$table->enum('star', array('no','yes'))->default('no');
			$table->dateTime('seen')->nullable();
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
		Schema::drop('email_receiver');
	}

}
