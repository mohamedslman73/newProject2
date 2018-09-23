<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppointmentStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('appointment_status', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('appointment_id');
			$table->integer('model_id');
			$table->string('model_type');
			$table->enum('status', array('pending','canceled','done','fail'));
			$table->text('comment', 65535);
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
		Schema::drop('appointment_status');
	}

}
