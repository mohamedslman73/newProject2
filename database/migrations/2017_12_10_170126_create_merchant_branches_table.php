<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantBranchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_branches', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('merchant_branches_merchant_id_foreign');
			$table->string('name_ar');
			$table->string('name_en');
			$table->string('address_ar');
			$table->string('address_en');
			$table->text('description_ar', 65535);
			$table->text('description_en', 65535);
			$table->enum('status', array('active','in-active'))->default('active');
			$table->float('latitude', 10, 0);
			$table->float('longitude', 10, 0);
			$table->integer('area_id')->unsigned()->index('merchant_branches_area_id_foreign');
			$table->integer('staff_id')->unsigned()->index('merchant_branches_staff_id_foreign');
			$table->integer('merchant_staff_id')->nullable();
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
		Schema::drop('merchant_branches');
	}

}
