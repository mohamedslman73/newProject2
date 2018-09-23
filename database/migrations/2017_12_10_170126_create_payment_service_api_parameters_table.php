<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentServiceApiParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_service_api_parameters', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('external_system_id')->comment('ID البراميتر عند ال SDK');
			$table->integer('payment_services_api_id');
			$table->string('name_ar');
			$table->string('name_en');
			$table->integer('position');
			$table->enum('visible', array('yes','no'));
			$table->enum('required', array('yes','no'));
			$table->string('type', 2);
			$table->enum('is_client_id', array('yes','no'));
			$table->string('default_value', 25);
			$table->integer('min_length');
			$table->integer('max_length');
			$table->integer('staff_id');
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
		Schema::drop('payment_service_api_parameters');
	}

}
