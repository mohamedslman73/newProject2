<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('sendermodel_type');
			$table->integer('sendermodel_id');
			$table->enum('sender_star', array('yes','no'))->default('no');
			$table->string('receivermodel_type');
			$table->integer('receivermodel_id')->nullable();
			$table->enum('receiver_star', array('yes','no'))->default('no');
			$table->string('subject');
			$table->text('body', 65535);
			$table->string('file')->nullable();
			$table->dateTime('seen')->nullable();
			$table->integer('seen_id')->nullable();
			$table->integer('parent_id')->nullable();
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
		Schema::drop('email');
	}

}
