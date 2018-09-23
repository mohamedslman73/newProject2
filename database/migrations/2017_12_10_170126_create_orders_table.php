<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_branch_id')->unsigned()->index('orders_merchant_branch_id_foreign');
			$table->integer('creatable_id');
			$table->string('creatable_type', 100);
			$table->text('comment', 65535)->nullable();
			$table->float('commission')->nullable();
			$table->enum('commission_type', array('fixed','percentage'))->nullable();
			$table->string('coupon', 100)->nullable();
			$table->float('total')->default(0.00);
			$table->enum('is_paid', array('yes','no'))->default('no');
			$table->string('qr_code', 191)->nullable();
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
		Schema::drop('orders');
	}

}
