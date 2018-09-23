<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommissionListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_list', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 150)->unique('name');
			$table->text('description', 65535);
			$table->enum('commission_type', array('one','multiple'));
			$table->text('condition_data', 65535);
			$table->integer('staff_id');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('commission_list');
	}

}
