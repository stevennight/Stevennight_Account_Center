<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthAuthCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * version: 1.0
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('oauth_auth_codes')){
            Schema::create('oauth_auth_codes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('client_id');
                $table->string('code',12);
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
