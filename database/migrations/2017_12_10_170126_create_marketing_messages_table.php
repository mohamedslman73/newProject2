<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMarketingMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('marketing_messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('message_type', array('sms','email','notification'));
			$table->string('title');
			$table->string('name_ar')->nullable();
			$table->string('name_en')->nullable();
			$table->text('content_ar', 65535);
			$table->text('content_en', 65535);
			$table->string('url_ar')->nullable();
			$table->string('url_en')->nullable();
			$table->string('image')->nullable();
			$table->enum('send_to', array('user','merchant','marketing_message_data'));
			$table->text('filter_data', 65535)->comment('marketing_data_id or serialize system filter');
			$table->dateTime('send_at')->nullable();
			$table->enum('status', array('request','in-progress','sent','error'))->default('request');
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
		Schema::drop('marketing_messages');
	}

}
