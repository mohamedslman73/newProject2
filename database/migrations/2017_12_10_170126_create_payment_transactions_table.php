<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_transactions', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('model_id');
			$table->string('model_type')->comment('USER MODEL');
			$table->enum('service_type', array('payment','inquiry','inquire'));
			$table->integer('external_system_id');
			$table->integer('payment_services_id');
			$table->float('amount', 10, 0);
			$table->float('total_amount', 10, 0);
			$table->text('request_map', 65535)->nullable();
			$table->enum('response_type', array('request','done','fail'))->default('request');
			$table->text('response', 65535)->nullable();
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
		Schema::drop('payment_transactions');
	}

}
