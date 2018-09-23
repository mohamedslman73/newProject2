<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAudioMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio_messages', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('msgsendermodel_id')->unsigned();
            $table->string('msgsendermodel_type', 255);
            $table->string('path', 255);
            $table->dateTime('seen')->nullable();
            $table->integer('seenby_id')->nullable();
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
        Schema::drop('audio_messages');
    }
}
