<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailReciverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_receiver', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id');
            $table->integer('receivermodel_id')->unsigned();
            $table->string('receivermodel_type', 255);
            $table->enum('star', array('no', 'yes'))->default('no');
            $table->dateTime('seen')->nullable();
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
        Schema::drop('email_receiver');
    }
}
