<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * version: 1.0
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username',255);
                $table->string('password',255);
                $table->integer('group');
                $table->integer('baned');
                $table->string('avatar',255);
                $table->string('email',255);
                $table->string('last_email_send',255);
                $table->integer('email_active');
                $table->string('QQ',20);
                $table->integer('QQ_active');
                $table->string('reg_address',255);
                $table->string('created_at',255);
                $table->string('updated_at',255);
                $table->string('last_login',255);
            });
            \Illuminate\Support\Facades\DB::table('users')->insert([
                'username' => 'administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('admin12345678'),
                'group' => 1,
                'baned' => 0,
                'avatar' => '/img/234051.jpg',
                'email' => 'admin@admin.com',
                'last_email_send' => '0',
                'email_active' => 0,
                'QQ' => '12345678',
                'QQ_active' => 0,
                'reg_address' => '127.0.0.1',
                'created_at' => time(),
                'updated_at' => time(),
                'last_login' => '0',
            ]);
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
