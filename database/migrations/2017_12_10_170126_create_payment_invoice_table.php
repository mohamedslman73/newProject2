<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentInvoiceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_invoice', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('payment_transaction_id');
			$table->integer('creatable_id');
			$table->string('creatable_type');
			$table->float('total', 10, 0);
			$table->float('total_amount', 10, 0);
			$table->enum('status', array('pending','paid','reverse'))->default('pending');
			$table->integer('wallet_settlement_id')->nullable();
			$table->text('wallet_settlement_data', 65535)->nullable();
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
		Schema::drop('payment_invoice');
	}

}
