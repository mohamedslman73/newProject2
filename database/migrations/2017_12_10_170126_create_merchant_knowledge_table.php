<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantKnowledgeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_knowledge', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name_ar', 191);
			$table->string('name_en', 191);
			$table->text('content_ar', 65535);
			$table->text('content_en', 65535);
			$table->integer('merchant_staff_id');
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
		Schema::drop('merchant_knowledge');
	}

}
