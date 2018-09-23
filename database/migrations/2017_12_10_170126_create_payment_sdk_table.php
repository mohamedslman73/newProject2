<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentSdkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_sdk', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('adapter_name', 50)->unique('adapter_name');
			$table->string('name');
			$table->text('description', 65535)->nullable();
			$table->text('address', 65535)->nullable();
			$table->string('logo')->nullable();
			$table->integer('area_id');
			$table->integer('staff_id');
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
		Schema::drop('payment_sdk');
	}

}
