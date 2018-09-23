<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('model_id')->nullable();
			$table->string('model_type')->nullable();
			$table->float('amount', 10, 0);
			$table->integer('from_id');
			$table->integer('to_id');
			$table->enum('type', array('wallet','cash'))->comment('الفلوس اتدفعت كاش ولا بالمحفظة');
			$table->enum('status', array('pending','paid','reverse'));
			$table->float('latitude', 10, 0);
			$table->float('longitude', 10, 0);
			$table->integer('creatable_id')->nullable();
			$table->string('creatable_type')->nullable();
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
		Schema::drop('transactions');
	}

}
