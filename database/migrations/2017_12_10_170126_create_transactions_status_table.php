<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions_status', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('user_type');
			$table->integer('transaction_id');
			$table->enum('status', array('pending','paid','reverse'));
			$table->text('comment', 65535)->nullable();
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
		Schema::drop('transactions_status');
	}

}
