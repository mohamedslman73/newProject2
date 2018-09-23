<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_staff', function (Blueprint $table) {
            $table->enum('language_key',['ar','en'])->default('ar');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->enum('language_key',['ar','en'])->default('ar');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('language_key',['ar','en'])->default('ar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_staff', function (Blueprint $table) {
            $table->dropColumn('language_key');
        });
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('language_key');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('language_key');
        });
    }
}
