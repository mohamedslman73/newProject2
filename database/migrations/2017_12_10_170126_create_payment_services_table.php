<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_services', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('payment_sdk_id')->nullable();
			$table->integer('payment_service_provider_id');
			$table->integer('payment_output_id');
			$table->integer('commission_list_id');
			$table->string('name_ar');
			$table->string('name_en');
			$table->text('description_ar', 65535)->nullable();
			$table->text('description_en', 65535)->nullable();
			$table->enum('request_amount_input', array('yes','no'))->default('no');
			$table->enum('status', array('active','in-active'))->default('in-active');
			$table->string('icon')->nullable();
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
		Schema::drop('payment_services');
	}

}
