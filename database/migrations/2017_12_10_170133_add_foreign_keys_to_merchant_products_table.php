<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMerchantProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_products', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('merchant_product_category_id')->references('id')->on('merchant_product_categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('merchant_products', function(Blueprint $table)
		{
			$table->dropForeign('merchant_products_merchant_id_foreign');
			$table->dropForeign('merchant_products_merchant_product_category_id_foreign');
		});
	}

}
