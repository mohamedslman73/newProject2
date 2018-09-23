<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAreasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('areas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('area_type_id')->unsigned()->index('areas_area_type_id_foreign');
			$table->string('name_ar');
			$table->string('name_en');
			$table->decimal('latitude', 10, 8);
			$table->decimal('longitude', 10, 8);
			$table->integer('parent_id')->unsigned()->nullable()->index('areas_parent_id_foreign');
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
		Schema::drop('areas');
	}

}
