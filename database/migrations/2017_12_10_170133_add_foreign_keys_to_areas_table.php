<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAreasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('areas', function(Blueprint $table)
		{
			$table->foreign('area_type_id')->references('id')->on('area_types')->onUpdate('RESTRICT')->onDelete('NO ACTION');
			$table->foreign('parent_id')->references('id')->on('areas')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('areas', function(Blueprint $table)
		{
			$table->dropForeign('areas_area_type_id_foreign');
			$table->dropForeign('areas_parent_id_foreign');
		});
	}

}
