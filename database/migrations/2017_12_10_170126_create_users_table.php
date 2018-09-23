<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('user_name', 100);
			$table->string('firstname', 100);
			$table->string('middlename', 100);
			$table->string('lastname');
			$table->string('email', 100)->unique();
			$table->string('mobile', 11)->unique();
			$table->string('password');
			$table->string('remember_token', 100);
			$table->string('image')->nullable();
			$table->enum('gender', array('male','female'));
			$table->string('national_id', 25);
			$table->date('birthdate');
			$table->string('address')->nullable();
			$table->enum('status', array('active','in-active'));
			$table->integer('parent_id')->nullable();
			$table->integer('nationality_id');
			$table->string('national_id_image');
			$table->bigInteger('facebook_user_id');
			$table->bigInteger('google_user_id');
			$table->dateTime('lastlogin')->nullable();
			$table->dateTime('verified_at')->nullable();
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
		Schema::drop('users');
	}

}
