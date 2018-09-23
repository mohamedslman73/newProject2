<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChatConversationSeenTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('chat_conversation_seen', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('model_id');
			$table->string('model_type', 100)->comment('USER MODEL');
			$table->integer('chat_conversation_id');
			$table->integer('last_chat_message_id');
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
		Schema::drop('chat_conversation_seen');
	}

}
