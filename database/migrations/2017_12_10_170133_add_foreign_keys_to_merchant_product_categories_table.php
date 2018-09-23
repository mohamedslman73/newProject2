<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMerchantProductCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_product_categories', function(Blueprint $table)
		{
			$table->foreign('approved_by_staff_id')->references('id')->on('staff')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('merchant_product_categories', function(Blueprint $table)
		{
			$table->dropForeign('merchant_product_categories_approved_by_staff_id_foreign');
			$table->dropForeign('merchant_product_categories_merchant_id_foreign');
		});
	}

}
