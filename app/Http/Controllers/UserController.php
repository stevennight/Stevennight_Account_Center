<?php
namespace App\Http\Controllers;

use App\Custom\Functions;
use App\Database\ConfigGlobalWebsite;
use App\Database\EmailVerifyToken;
use App\Database\ResetPasswordToken;
use App\Database\Users;
use App\Mail\MailResetPasswordNewPass;
use App\Mail\MailVerifyTokenMail;
use App\Mail\MailResetPasswordToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller{

    /**
     * 显示用户资料的页面
     * A page of displaying the users' profile
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileShow(Request $request){

        if($request->isMethod('post')){
            $userinfo = Users::where('id','=',$request->session()->get('member.userid'))->first();
            $this->validate($request, [
                'username' => 'required|alpha_dash|between:3,15',
                'QQ' => 'required|between:5,15|regex:/^[0-9]+$/u',
                'email' => 'required|between:5,100|email',
            ],[
                'required' => trans('view.profile.required'),
                'alpha_dash' => trans('view.profile.alpha_dash'),
                'between' => trans('view.profile.between'),
                'email' => trans('view.profile.emailcheck'),
                'regex' => trans('view.profile.regex'),
            ],[
                'username' => trans('view.profile.username'),
                'QQ' => trans('view.profile.QQ'),
                'email' => trans('view.profile.email'),
            ]);

            $renew_num = 0;
            if($request->get('username') != $request->session()->get('member.username')){
                if(Users::where('username','=',$request->user())->first() == null){
                    $userinfo->username = $request->get('username');
                    $renew_num++;
                }else{
                    return view('member.profile')->withErrors(['infomation' => trans('view.profile.username_exist')]);
                }
            }

            if($userinfo->QQ_active == 0){
                if($request->get('QQ') != $request->session()->get('member.QQ')){
                    if(Users::where('QQ','=',$request->get('QQ'))->where('QQ_active','=',1)->first() == null){
                        $userinfo->QQ = $request->get('QQ');
                        $renew_num++;
                    }else{
                        return view('member.profile')->withErrors(['infomation' => trans('view.profile.QQ_exist')]);
                    }
                }
            }

            if($userinfo->email_active == 0){
                if($request->get('email') != $request->session()->get('member.email')){
                    if(Users::where('email','=',$request->get('email'))->where('email_active','=',1)->first() == null){
                        $userinfo->email = $request->get('email');
                        $renew_num++;
                    }else{
                        return view('member.profile')->withErrors(['infomation' => trans('view.profile.email_exist')]);
                    }
                }
            }

            //文件上传   upload file.
            $file = $request->file('avatar');
            if($file!=null){
                if(!$file->isValid()){
                    return redirect()->back()->withErrors(['upload_cover_error' => trans('view.form.roomedit.upload_cover_error')])->withInput();
                }
                $extension = $file->extension();
                if(!in_array($extension,['jpg','png','jpeg','gif'])){
                    return redirect()->back()->withErrors(['invalid_extension' => trans('view.form.roomedit.invalid_extension')])->withInput();
                }
                $path = $file->store('user/avatar');
                $userinfo->avatar = $request->session()->get('config')->files_path.$path;
                $renew_num++;
            }

            if($userinfo->save()){
                Functions::refressUserinfoSession($request,$request->session()->get('member.userid'));

                return view('member.profile')->withErrors(['infomation' => trans('view.profile.save_success').'('.$renew_num.trans('view.profile.items').')']);
            }else{
                return view('member.profile')->withErrors(['infomation' => trans('view.profile.save_fail')]);
            }
        }

        return view('member.profile');
    }

    /**
     * 修改密码页面并处理
     * Changing password page and handle the request.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rePassword(Request $request){

        if($request->isMethod('post')){
            $this->validate($request,[
               'oldpass' => 'required|between:6,18',
                'newpass' => 'required|between:6,18',
                'newpass_confirmation' => 'required|between:6,18|same:newpass',
            ],[
                'required' => trans('view.form.repassword.required'),
                'between' => trans('view.form.repassword.between'),
                'same' => trans('view.form.repassword.same'),
            ],[
                'oldpass' => trans('view.form.repassword.oldpass'),
                'newpass' => trans('view.form.repassword.newpass'),
                'newpass_confirmation' => trans('view.form.repassword.newpass_confirmation'),
            ]);

            $userinfo = Users::where('id','=',$request->session()->get('member.userid'))->first();
            $dbOldpass = $userinfo->password;
            if(!Hash::check($request->get('oldpass'),$dbOldpass)){
                return redirect()->route('repassword')->withErrors(['old_password_wrong' => trans('view.form.repassword.old_password_wrong')])->withInput();
            }

            $userinfo->password = Hash::make($request->get('newpass'));
            if($userinfo->save()){
                //修改成功  repassword is success.
                $username = $request->session()->get('member.username');
                $request->session()->forget('member');
                return redirect()->route('login')->withErrors(['repass_success_must_login_again' => trans('view.form.repassword.repass_success_must_login_again')])->withInput(['username' => $username]);
            }else{
                return redirect()->route('repassword')->withErrors(['save_fail' => trans('view.form.repassword.save_fail')])->withInput();
            }

        }

        return view('member.repassword');
    }

    /**
     * 用户邮件验证链接
     * A link of checking the eamil valid which was send to users' email
     *
     * @param $username
     * @param $email
     * @param $token
     * @return string
     */
    public function emailVerify($userid,$token){
        $user = Users::where('id','=',$userid)->first();
        $token_table = EmailVerifyToken::where('userid','=',$userid)->first();

        //判断用户存在   if users if not exist
        if($user==null){
            return trans('view.email.verify.user_not_found');
        }

        //判断token存在   if token not exist
        if($token_table==null){
            return trans('view.email.verify.token_not_found');
        }

        //判断token记录是否与链接提供的token一致  if token from link is not same to the token from database.
        if($token != $token_table->token){
            return trans('view.email.verify.incorrect_token');
        }

        //判断token 是否过期   if the token is expire
        if(time()-$token_table->updated_at > ConfigGlobalWebsite::all()->first()->email_token_expire){
            return trans('view.email.verify.expire_token');
        }

        //判断用户是否已验证邮箱   if user is active of email
        if($user->email_active == 1){
            $token_table->invalid=1;
            $token_table->save();
            return trans('view.email.verify.has_active');
        }

        //判断用户绑定邮箱与token记录的邮箱是否一致   if token with email is not same to email of user information
        if($user->email != $token_table->email){
            return trans('view.email.verify.incorrect_email_with_account');
        }

        //判断邮箱是否被他人绑定验证过   if email is actived by other
        if(Users::where('email','=',$user->email)->where('email_active','=',1)->first() != null){
            return trans('view.email.verify.email_has_bind_by_other');
        }

        $user->email_active = 1;
        $user->save();
        $token_table->invalid=1;
        $token_table->save();

        return 'success';
    }

    /**生成令牌并发送激活邮件
     * generate a token and send a activation email to guest
     *
     * @param Request $request
     * @return string
     */
    public function emailVerifySend(Request $request){

        $userid = $request->session()->get('member.userid');
        $username = $request->session()->get('member.username');
        $email = $request->session()->get('member.email');

        $user = Users::where('id','=',$userid)->first();
        if(time()-$user->last_email_send > ConfigGlobalWebsite::all()->first()->email_send_interval){
            if($user->email_active == 1){
                $email_send_status =  trans('view.email.verify.has_active');
            }else{
                $token = substr(md5($username.time().rand(1000,9999)),rand(0,23),8);
                $email_verify_token = EmailVerifyToken::where('userid','=',$userid)->first();
                if($email_verify_token == null)
                {
                    $email_verify_token = new EmailVerifyToken();
                }
                $email_verify_token->userid = $userid;
                $email_verify_token->email = $email;
                $email_verify_token->token = $token;
                $email_verify_token->invalid = 0;
                if($email_verify_token->save()){
                    $mailval = [
                        'userid' => $userid,
                        'username' => $username,
                        'email' => $email,
                        'token' => $token,
                    ];
                    Mail::to($email)->send(new MailVerifyTokenMail($mailval));

                    $user->last_email_send = time();
                    $user->save();

                    $email_send_status = trans('view.email.verify.send_success');
                }else{
                    $email_send_status = trans('view.email.verify.send_fail');
                }
            }
        }else{
            $email_send_status = trans('view.email.verify.interval_too_short');
        }

        return redirect()->back()->withErrors(['information' => $email_send_status]);
    }

    /*
     * 发送重置密码令牌邮件
     * A function to send a reset password token to user email.
     */
    public function resetPasswordEmailSend(Request $request){

        if($request->isMethod('post')){

            $this->validate($request,[
                'username' => 'required|alpha_dash|between:3,15',
                'email' => 'required|between:5,100|email',
            ],[
                'required' => trans('view.resetPassword.required'),
                'alpha_dash' => trans('view.resetPassword.alpha_dash'),
                'between' => trans('view.resetPassword.between'),
                'email' => trans('view.resetPassword.emailcheck'),
            ],[
                'username' => trans('view.resetPassword.username'),
                'email' => trans('view.resetPassword.email')
            ]);

            $db_users = Users::where('username','=',$request->get('username'))->first();
            if($db_users == null){
                return redirect()->route('resetPasswordEmailSend')->withErrors(['user_not_found' => trans('view.resetPassword.user_not_found')]);
            }
            if($request->get('email') != $db_users->email){
                return redirect()->route('resetPasswordEmailSend')->withErrors(['incorrect_email' => trans('view.resetPassword.incorrect_email')]);
            }
            if($db_users->email_active != 1){
                return redirect()->route('resetPasswordEmailSend')->withErrors(['must_be_active_email' => trans('view.resetPassword.must_be_active_email')]);
            }

            if(time()-$db_users->last_email_send < ConfigGlobalWebsite::all()->first()->email_send_interval) {
                return redirect()->route('resetPasswordEmailSend')->withErrors(['send_interval_too_short' => trans('view.resetPassword.send_interval_too_short')]);
            }

            //生成验证码   generate a verify code
            $token = md5($db_users->username.time()).md5($db_users->email.time());
            $db_reset_password_token = ResetPasswordToken::where('userid','=',$db_users->id)->first();
            if($db_reset_password_token == null){
                $db_reset_password_token = new ResetPasswordToken();
            }
            $db_reset_password_token->userid = $db_users->id;
            $db_reset_password_token->token = $token;
            $db_reset_password_token->expires_at = time() + ConfigGlobalWebsite::all()->first()->email_token_expire;
            if(!$db_reset_password_token->save()){
                return redirect()->route('resetPasswordEmailSend')->withErrors(['data_save_error' => trans('view.resetPassword.data_save_error')]);
            }

            //发送邮件  send email.
            $username = $db_users->username;
            $userid = $db_users->id;
            $mailval = [
                'username' => $username,
                'userid' => $userid,
                'token' => $token,
            ];
            try {
                Mail::to($db_users->email)->send(new MailResetPasswordToken($mailval));
            }catch(\Exception $e){
                return redirect()->route('resetPasswordEmailSend')->withErrors(['send_mail_error' => trans('view.resetPassword.send_mail_error')]);
            }

            $db_users->last_email_send = time();
            $db_users->save();
            return redirect()->route('resetPasswordEmailSend')->withErrors(['send_success' => trans('view.resetPassword.send_success')]);
        }

        return view('member.forgetPassword');
    }

    /*
     * 重置密码令牌验证，并修改密码，新密码发送至用户邮箱。
     * Verify the reset password token a change the password. The new password would be send to user email.s
     */
    public function resetPasswordVerify(Request $request,$userid,$token){
        $db_reset_password_token = ResetPasswordToken::where('userid','=',$userid)->first();
        if($db_reset_password_token == null){
            return redirect()->route('resetPasswordEmailSend')->withErrors(['token_not_found' => trans('view.resetPassword.token_not_found')]);
        }
        if($db_reset_password_token->token != $token){
            return redirect()->route('resetPasswordEmailSend')->withErrors(['incorrect_token' => trans('view.resetPassword.incorrect_token')]);
        }
        $db_users = Users::where('id','=',$userid)->first();
        if($db_users == null){
            return redirect()->route('resetPasswordEmailSend')->withErrors(['user_not_found' => trans('view.resetPassword.user_not_found')]);
        }
        $oldpassHash = $db_users->password;
        $newPass = substr(md5(md5(rand(100000,999999)).md5(time())),rand(0,14),rand(10,18));
        $db_users->password = Hash::make($newPass);
        if(!$db_users->save()){
            return redirect()->route('resetPasswordEmailSend')->withErrors(['data_save_error' => trans('view.resetPassword.data_save_error')]);
        }

        //发送邮件  send email.
        $username = $db_users->username;
        $mailval = [
            'username' => $username,
            'newPass' => $newPass,
        ];
        try {
            Mail::to($db_users->email)->send(new MailResetPasswordNewPass($mailval));
        }catch(\Exception $e){
            $db_users->password = $oldpassHash;
            $db_users->save();
            return redirect()->route('resetPasswordEmailSend')->withErrors(['send_mail_error' => trans('view.resetPassword.send_mail_error')]);
        }

        $db_reset_password_token->delete();
        return redirect()->route('resetPasswordEmailSend')->withErrors(['reset_password_success' => trans('view.resetPassword.reset_password_success')]);
    }
}