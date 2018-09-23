<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantStaffGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_staff_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->integer('merchant_id')->unsigned()->index('merchant_staff_group_metchant_id_foreign');
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
		Schema::drop('merchant_staff_groups');
	}

}
