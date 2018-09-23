<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentServiceApisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_service_apis', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('payment_service_id');
			$table->enum('service_type', array('payment','inquiry','inquire'));
			$table->string('name');
			$table->text('description', 65535)->nullable();
			$table->integer('external_system_id')->comment('ID الخدمة عند المقدمين');
			$table->integer('price_type');
			$table->float('service_value', 10, 0);
			$table->string('service_value_list');
			$table->float('min_value', 10, 0);
			$table->float('max_value', 10, 0);
			$table->integer('commission_type');
			$table->integer('commission_value_type');
			$table->float('fixed_commission', 10, 0);
			$table->float('default_commission', 10, 0);
			$table->float('from_commission', 10, 0);
			$table->float('to_commission', 10, 0);
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
		Schema::drop('payment_service_apis');
	}

}
