<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMerchantPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_plans', function(Blueprint $table)
		{
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
		Schema::table('merchant_plans', function(Blueprint $table)
		{
			$table->dropForeign('merchant_plans_staff_id_foreign');
		});
	}

}
