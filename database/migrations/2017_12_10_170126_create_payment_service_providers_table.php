<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentServiceProvidersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_service_providers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('payment_service_provider_category_id');
			$table->string('name_ar');
			$table->string('name_en');
			$table->text('description_ar', 65535)->nullable();
			$table->text('description_en', 65535)->nullable();
			$table->string('logo')->nullable();
			$table->enum('status', array('active','in-active'));
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
		Schema::drop('payment_service_providers');
	}

}
