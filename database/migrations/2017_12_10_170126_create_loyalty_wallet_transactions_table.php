<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoyaltyWalletTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loyalty_wallet_transactions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('model_id')->nullable();
			$table->string('model_type')->nullable();
			$table->float('amount', 10, 0);
			$table->integer('from_id');
			$table->integer('to_id');
			$table->enum('type', array('wallet','cash'));
			$table->enum('status', array('pending','paid','reverse'));
			$table->float('latitude', 10, 0);
			$table->float('longitude', 10, 0);
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
		Schema::drop('loyalty_wallet_transactions');
	}

}
