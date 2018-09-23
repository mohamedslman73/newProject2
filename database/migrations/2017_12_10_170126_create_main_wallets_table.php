<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMainWalletsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('main_wallets', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('unique_name', 50)->unique('unique_name');
			$table->string('name', 100);
			$table->text('description', 65535)->nullable();
			$table->enum('transfer_in', array('yes','no'))->default('no');
			$table->enum('transfer_out', array('yes','no'))->default('no');
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
		Schema::drop('main_wallets');
	}

}
