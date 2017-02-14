<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailVerifyToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_verify_token', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userid');
            $table->string('email',255);
            $table->string('token',255);
            $table->string('created_at',255);
            $table->string('updated_at',255);
            $table->integer('invalid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('email_verify_token');
    }
}
