<?php

namespace App\Http\Middleware;

use App\Custom\Functions;

class LoginStatusCheck{
    public function handle($request, \Closure $next){

        if($request->is('login')||$request->is('register')||$request->is('password/forget')){
            if($request->session()->has('member')){
                return redirect() -> route('profile');
            }
            return $next($request);
        }

        if(!$request->session()->has('member')){
            return redirect() -> route('login')->withErrors(['login_required'=> trans('view.form.login.login_required')])->withInput(['redirect' => $request->path()]);
        }

        if(!Functions::refressUserinfoSession($request,$request->session()->get('member.userid'))){
            $request->session()->forget('member');
            return redirect() -> route('login')->withErrors(['login_required'=> trans('view.form.login.error_login')])->withInput(['redirect' => $request->path()]);
        }
        if($request->session()->get('member.baned') == 1){
            $request->session()->forget('member');
            return redirect()->route('login')->withErrors(['user_baned' => trans('view.form.login.user_baned')])->withInput();
        }

        return $next($request);
    }
}