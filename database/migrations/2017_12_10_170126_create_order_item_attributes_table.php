<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderItemAttributesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_item_attributes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order_item_id');
			$table->integer('attribute_id');
			$table->text('attribute_value', 65535);
			$table->text('attribute_data', 65535);
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
		Schema::drop('order_item_attributes');
	}

}
