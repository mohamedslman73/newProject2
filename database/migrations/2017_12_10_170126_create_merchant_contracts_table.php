<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantContractsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_contracts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('merchant_contracts_merchant_id_foreign');
			$table->integer('plan_id')->unsigned();
			$table->text('description', 65535)->nullable();
			$table->float('price');
			$table->date('start_date');
			$table->date('end_date');
			$table->string('admin_name');
			$table->string('admin_job_title');
			$table->integer('staff_id')->unsigned()->index('merchant_contracts_staff_id_foreign');
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
		Schema::drop('merchant_contracts');
	}

}
