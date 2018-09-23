<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_plans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->text('description', 65535);
			$table->integer('months')->unsigned();
			$table->float('amount', 10, 0);
			$table->integer('staff_id')->unsigned()->index('merchant_plans_staff_id_foreign');
			$table->text('type', 65535)->comment('serialize bee and merchant');
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
		Schema::drop('merchant_plans');
	}

}
