<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthClients extends Migration
{
    /**
     * Run the migrations.
     *
     * version: 1.0
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('oauth_clients')){
            Schema::create('oauth_clients', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('name',255);
                $table->string('secret',100);
                $table->text('userurl');
                $table->text('redirect');
                $table->string('created_at',255);
                $table->string('updated_at',255);
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
