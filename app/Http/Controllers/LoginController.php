<?php

namespace App\Http\Controllers;

use App\Custom\Functions;
use App\Database\ConfigGlobalWebsite;
use App\Database\EmailVerifyToken;
use App\Mail\MailVerifyTokenMail;
use App\User;
use App\Http\Controllers\Controller as Controller;
use App\Database\Users as Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Location;

/**
 * 处理登录/注册等账户处理请求的类
 * a class of handling the login/register request and other requests to change account infomation.
 *
 * Class LoginController
 * @package App\Http\Controllers
 */
class LoginController extends  Controller{

    function __construct(){

    }

    /**
     * 显示注册页面以及处理注册请求。
     * display the register page and handle the register request.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function registerPage(Request $request){

        if($request->isMethod('post')){
            $this->validate($request, [
                'username' => 'required|alpha_dash|between:3,15',
                'password' => 'required|between:6,18',
                'password_confirmation' => 'required|same:password',
                'QQ' => 'required|between:5,15|regex:/^[0-9]+$/u',
                'email' => 'required|between:5,100|email',
                'accept_term' => 'required',
            ],[
                'required' => trans('view.form.register.required'),
                'alpha_dash' => trans('view.form.register.alpha_dash'),
                'between' => trans('view.form.register.between'),
                'same' => trans('view.form.register.same'),
                'email' => trans('view.form.register.emailcheck'),
                'regex' => trans('view.form.register.regex'),
             ],[
                'username' => trans('view.form.register.username'),
                'password' => trans('view.form.register.password'),
                'password_confirmation' => trans('view.form.register.password_confirmation'),
                'QQ' => trans('view.form.register.QQ'),
                'email' => trans('view.form.register.email'),
                'accept_term' => trans('view.form.register.accept_term'),
            ]);

            $userinfo = Users::where('username','=',$request->get('username'))->first();
            if($userinfo!=null){
                return redirect()->route('register')->withErrors(['username_exist' => trans('view.form.register.username_exist')])->withInput();
            }
            $userinfo = Users::where('QQ','=',$request->get('QQ'))->where('QQ_active','=',1)->first();
            if($userinfo!=null){
                return redirect()->route('register')->withErrors(['QQ_exist' => trans('view.form.register.QQ_exist')])->withInput();
            }
            $userinfo = Users::where('email','=',$request->get('email'))->where('email_active','=',1)->first();
            if($userinfo!=null){
                return redirect()->route('register')->withErrors(['email_exist' => trans('view.form.register.email_exist')])->withInput();
            }

            //保存数据 save data.
            $userinfo = new Users();
            $userinfo->username = $request->get('username');
            $userinfo->password = Hash::make($request->get('password'));
            $userinfo->group = 0;
            $userinfo->baned = 0;
            $userinfo->avatar = $request->session()->get('config')->avatar_default;
            $userinfo->email = $request->get('email');
            $userinfo->last_email_send = 0;
            $userinfo->email_active = 0;
            $userinfo->QQ = $request->get('QQ');
            $userinfo->QQ_active = 0;
            $userinfo->reg_address = $request->getClientIp();
            $userinfo->last_login = '0';
            if($userinfo->save()){
                //成功注册 register successful

                return redirect()->route('login')->withErrors(['register_success' => trans('view.form.register.success')])->withInput();
            }else{
                //保存失败 save in fault
                return redirect()->route('register')->withErrors(['save_fail' => trans('view.form.register.save_fail')])->withInput();
            }
        }

        return view('register');
    }


    /**
     * 显示登录页面以及处理登录请求。
     * display the login page and handle login request.
     *
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function loginPage(Request $request){

        //$redirect接收的数据必须为页面完整的路径。  $redirect must recieve a full path of the page.sss

        if(isset($_GET['redirect'])){
            $redirect=$_GET['redirect'];
        }elseif($request->has('redirect')){
            $redirect = $request->get('redirect');
        }else{
            $redirect='';
        }

        if($request->isMethod('post')){

            $this->validate($request,[
                'username' => 'required|alpha_dash|between:3,15',
                'password' => 'required|between:6,18'
            ],[
                'required' => trans('view.form.login.required'),
                'alpha_dash' => trans('view.form.login.alpha_dash'),
                'between' => trans('view.form.login.between'),
            ],[
                'username' => trans('view.form.login.username'),
                'password' => trans('view.form.login.password'),
            ]);

            $userinfo = Users::where('username','=',$request->get('username'))->first();
            if($userinfo==null){
                return redirect()->route('login')->withErrors(['user_not_found' => trans('view.form.login.user_not_found')])->withInput();
            }

            $userpass = $userinfo->password;
            if(!Hash::check($request->get('password'),$userpass)){
                return redirect()->route('login')->withErrors(['user_password_wrong' => trans('view.form.login.user_password_wrong')])->withInput();
            }

            if($userinfo->baned == 1){
                return redirect()->route('login')->withErrors(['user_baned' => trans('view.form.login.user_baned')])->withInput();
            }

            //更新登录时间 update the last login time.
            $userinfo->last_login = time();
            $userinfo->save();

            //处理session   handle sessions
            $request->session()->regenerate();
            Functions::refressUserinfoSession($request,$userinfo->id);

            //重定向  redirect
            if($redirect=='') {
                return redirect()->route('index');
            }else{
                return redirect($redirect);
            }
        }

        return view('login',['redirect'=>$redirect]);
    }

    public function logoutPage(Request $request){

        $request->session()->forget('member');

        return redirect()->route('index');

    }

}