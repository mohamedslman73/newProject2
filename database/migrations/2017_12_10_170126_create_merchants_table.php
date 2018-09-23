<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchants', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('is_reseller', array('active','in-active'));
			$table->integer('area_id')->unsigned()->index('merchants_area_id_foreign');
			$table->string('name_ar');
			$table->string('name_en');
			$table->text('description_ar', 65535);
			$table->text('description_en', 65535);
			$table->string('address');
			$table->string('logo');
			$table->integer('merchant_contract_id')->unsigned()->nullable()->index('merchants_merchant_contract_id_foreign');
			$table->integer('merchant_category_id')->unsigned()->index('merchants_merchant_category_id_foreign');
			$table->text('attribute_categories', 65535)->nullable();
			$table->enum('status', array('active','in-active'))->default('in-active');
			$table->integer('staff_id')->unsigned()->index('merchants_staff_id_foreign');
			$table->integer('parent_id')->unsigned()->nullable()->index('merchants_parent_id_foreign');
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
		Schema::drop('merchants');
	}

}
