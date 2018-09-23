<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdvertisementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('advertisements', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->string('image');
			$table->float('width', 10, 0)->nullable();
			$table->float('height', 10, 0)->nullable();
			$table->string('route', 500)->nullable()->comment('SET Date Like (system.user.index,system.user.create)');
			$table->integer('route_id')->nullable();
			$table->text('comment', 65535)->nullable();
			$table->enum('status', array('active','in-active'))->default('active');
			$table->enum('type', array('merchant','user'))->default('user');
			$table->float('total_amount', 10, 0);
			$table->integer('merchant_id')->nullable()->comment('optinal is this add to merchant');
			$table->string('merchant_staff_id', 191)->nullable();
			$table->integer('staff_id');
			$table->date('from_date');
			$table->date('to_date');
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
		Schema::drop('advertisements');
	}

}
