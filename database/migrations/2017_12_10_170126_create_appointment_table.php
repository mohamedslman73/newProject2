<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppointmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('appointment', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('model_type');
			$table->integer('model_id');
			$table->dateTime('appointment_date_time')->nullable();
			$table->text('description', 65535);
			$table->enum('status', array('pending','canceled','done','fail'))->default('pending');
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
		Schema::drop('appointment');
	}

}
