<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantProductCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_product_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('merchant_product_categories_merchant_id_foreign');
			$table->string('name_ar');
			$table->string('name_en');
			$table->text('description_ar', 65535);
			$table->text('description_en', 65535);
			$table->string('icon')->nullable();
			$table->enum('status', array('active','in-active'))->default('active');
			$table->integer('created_by_merchant_staff_id')->nullable();
			$table->integer('approved_by_staff_id')->unsigned()->nullable()->index('merchant_product_categories_approved_by_staff_id_foreign');
			$table->dateTime('approved_at')->nullable();
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
		Schema::drop('merchant_product_categories');
	}

}
