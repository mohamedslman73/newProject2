<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMerchantContractsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_contracts', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('staff_id')->references('id')->on('staff')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('merchant_contracts', function(Blueprint $table)
		{
			$table->dropForeign('merchant_contracts_merchant_id_foreign');
			$table->dropForeign('merchant_contracts_staff_id_foreign');
		});
	}

}
