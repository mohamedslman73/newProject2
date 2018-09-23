<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWalletTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wallet', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->enum('type', array('payment','e-commerce','loyalty'))->default('payment')->comment('نوع الحساب زي توفير وجاري');
			$table->integer('walletowner_id');
			$table->string('walletowner_type');
			$table->float('balance', 10, 0)->default(0);
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
		Schema::drop('wallet');
	}

}
