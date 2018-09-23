<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMerchantBranchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_branches', function(Blueprint $table)
		{
			$table->foreign('area_id')->references('id')->on('areas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
		Schema::table('merchant_branches', function(Blueprint $table)
		{
			$table->dropForeign('merchant_branches_area_id_foreign');
			$table->dropForeign('merchant_branches_merchant_id_foreign');
			$table->dropForeign('merchant_branches_staff_id_foreign');
		});
	}

}
