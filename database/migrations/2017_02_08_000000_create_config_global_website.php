<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigGlobalWebsite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('config_global_website');
        Schema::create('config_global_website', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',15);
            $table->string('email',255);
            $table->integer('email_send_interval');
            $table->integer('email_token_expire');
            $table->integer('oauth_auth_code_expire');
            $table->integer('oauth_access_token_expire');
            $table->integer('oauth_update_token_expire');
            $table->string('files_path');
            $table->string('avatar_default');
        });
        \Illuminate\Support\Facades\DB::table('config_global_website')->insert([
            'name' => '网站名称',
            'email' => 'admin@admin.com',
            'email_send_interval' => 300,
            'email_token_expire' => 3600,
            'oauth_auth_code_expire' => 300,
            'oauth_access_token_expire' => 86400,
            'oauth_update_token_expire' => 86400,
            'files_path' => '/files/',
            'avatar_default' => '/img/234051.jpg',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('config_global_website');
    }
}
