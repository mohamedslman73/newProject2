<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChatConversationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('chat_conversation', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('from_id');
			$table->string('from_type', 100);
			$table->integer('to_id');
			$table->string('to_type', 100);
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
		Schema::drop('chat_conversation');
	}

}
