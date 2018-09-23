<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStaffTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('staff', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('firstname');
			$table->string('lastname');
			$table->string('national_id', 25)->unique();
			$table->string('email', 191)->unique();
			$table->string('mobile', 100);
			$table->string('avatar')->nullable();
			$table->enum('gender', array('male','female'));
			$table->date('birthdate')->nullable();
			$table->string('address')->nullable();
			$table->string('password');
			$table->string('remember_token', 100)->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('job_title')->nullable();
			$table->enum('status', array('active','in-active'))->default('active');
			$table->integer('language_id')->nullable();
			$table->integer('permission_group_id');
			$table->boolean('menu_type');
			$table->dateTime('lastlogin')->nullable();
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
		Schema::drop('staff');
	}

}
