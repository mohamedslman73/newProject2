<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_products', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('merchant_products_merchant_id_foreign');
			$table->integer('merchant_product_category_id')->unsigned()->index('merchant_products_merchant_product_category_id_foreign');
			$table->string('name_ar');
			$table->string('name_en');
			$table->text('description_ar', 65535);
			$table->text('description_en', 65535);
			$table->float('price', 10, 0);
			$table->integer('created_by_merchant_staff_id')->nullable();
			$table->integer('approved_by_staff_id')->nullable();
			$table->dateTime('approved_at')->nullable();
			$table->enum('status', array('active','in-active'))->default('active');
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
		Schema::drop('merchant_products');
	}

}
