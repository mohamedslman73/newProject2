<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChatSocketAccessTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('chat_socket_access', function(Blueprint $table)
		{
			$table->string('id', 191)->unique('id');
			$table->integer('model_id')->comment('USER MODAL (MERCHANT STAFF,USER,STAFF)');
			$table->string('model_type', 100)->comment('USER MODAL (MERCHANT STAFF,USER,STAFF)');
			$table->integer('chat_conversation_id');
			$table->string('socket_id', 100)->nullable()->unique('socket_id');
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
		Schema::drop('chat_socket_access');
	}

}
