<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWalletSettlementTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wallet_settlement', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('wallet_id')->nullable();
			$table->enum('status', array('processing','error','done'))->default('processing');
			$table->float('system_commission', 10, 0);
			$table->float('merchant_commission', 10, 0);
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
		Schema::drop('wallet_settlement');
	}

}
