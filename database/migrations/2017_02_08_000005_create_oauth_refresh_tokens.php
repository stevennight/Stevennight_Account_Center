<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthRefreshTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * version: 1.0
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('oauth_refresh_tokens')){
            Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
                $table->increments('id');
                $table->string('access_token_id',255);
                $table->string('token',255);
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
        Schema::drop('oauth_refresh_tokens');
    }
}
