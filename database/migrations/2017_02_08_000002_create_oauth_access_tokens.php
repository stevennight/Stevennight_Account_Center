<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * version: 1.0
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('oauth_access_tokens')){
            Schema::create('oauth_access_tokens', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('client_id');
                $table->string('token',255);
                $table->string('created_at',255);
                $table->string('updated_at',255);
                $table->string('expires_at',255);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
