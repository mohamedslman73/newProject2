<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductAttributesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_attributes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('product_id');
			$table->integer('attribute_id');
			$table->boolean('required');
			$table->integer('selected_attribute_value')->nullable();
			$table->boolean('stock');
			$table->float('quantity')->nullable();
			$table->float('plus_price')->nullable();
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
		Schema::drop('product_attributes');
	}

}
