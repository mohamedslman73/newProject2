<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMerchantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchants', function(Blueprint $table)
		{
			$table->foreign('area_id')->references('id')->on('areas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('merchant_category_id')->references('id')->on('merchant_categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('merchant_contract_id')->references('id')->on('merchant_contracts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('parent_id')->references('id')->on('merchants')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('staff_id')->references('id')->on('staff')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('merchants', function(Blueprint $table)
		{
			$table->dropForeign('merchants_area_id_foreign');
			$table->dropForeign('merchants_merchant_category_id_foreign');
			$table->dropForeign('merchants_merchant_contract_id_foreign');
			$table->dropForeign('merchants_parent_id_foreign');
			$table->dropForeign('merchants_staff_id_foreign');
		});
	}

}
