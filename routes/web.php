<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['middleware' => ['ConfigToSession']],function(){
    Route::group(['middleware'=>['LoginStatusCheck']],function(){

        //用户页面  user's center
        Route::group(['prefix'=>'member'],function(){
            //用户资料  user's profile
            Route::match(['get','post'],'profile','UserController@profileShow')->name('profile');
            //用户修改密码  edit user's password
            Route::match(['get','post'],'profile/repassword','UserController@rePassword')->name('repassword');
        });
    });

    Route::group(['prefix'=>'oauth'],function(){
        //认证页面   Authencation page
        Route::match(['get','post'],'/','OAuthController@OAuth')->name('OAuthIndex');
        //认证api，给予AuthCode，返回access_tokens
        Route::match(['post'],'api/getaccesstoken','OAuthController@getOAuthAccessToken')->name('ApiAccessTokenGet');
        //用户信息获取api，传输用户信息。
        Route::match(['post'],'api/getuserinfo','OAuthController@getUserInfo')->name('ApiUserInfo');
        //用户头像
        Route::get('api/getavatar/{userid}','OAuthController@getAvatar')->name('ApiAvatar');
    });

    //首页
    Route::get('/', function (Request $requesta) {
        return view('layouts.global');
    })->name('index');
    //用户登录  login page
    Route::match(['get','post'],'login','LoginController@loginPage')->name('login')->middleware('LoginStatusCheck');
    //用户注册  register page
    Route::match(['get','post'],'register','LoginController@registerPage')->name('register')->middleware('LoginStatusCheck');
    //用户登出  logout page
    Route::match(['get'],'logout','LoginController@logoutPage')->name('logout')->middleware('LoginStatusCheck');
    //验证邮箱验证链接  emailverify link
    Route::match('get','verify/{userid}/{token}','UserController@emailVerify')->name('email_verify');
    //发送邮箱验证链接 send activation email.
    Route::match('get','verify_send','UserController@emailVerifySend')->name('send_email_verify')->middleware('LoginStatusCheck');
    //找回密码   forget password and find it page.
    Route::match(['get','post'],'password/forget','UserController@resetPasswordEmailSend')->name('resetPasswordEmailSend')->middleware('LoginStatusCheck');
    //重置密码密码   forget password and find it page.
    Route::match(['get'],'password/forget/{userid}/{token}','UserController@resetPasswordVerify')->name('resetPasswordFunction');
});

Route::get('error','Controller@error')->name('error');