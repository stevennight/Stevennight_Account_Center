<?php
namespace App\Http\Controllers;

use App\Database\ConfigGlobalWebsite;
use App\Database\OAuthAccessTokens;
use App\Database\OAuthAuthCodes;
use App\Database\OAuthClients;
use App\Database\OAuthRefreshTokens;
use App\Database\Users;
use Illuminate\Http\Request;

class OAuthController extends Controller{

    public function OAuth(Request $request){
        //临时
        //$request->session()->put('clientid',1);

        if($request->has('clientid')){
            $request->session()->put('clientid',$request->get('clientid'));
        }

        if($request->session()->has('clientid')){
            $clientid = $request->session()->get('clientid');
        }else{
            return redirect()->route('index');
        }

        $client = OAuthClients::where('id','=',$clientid)->first();
        if($client == null){
            return 'Incorrect Oauth Client Id. Contact The Administrator of The Website Where You Are Come From.';
        }

        if(!$request->session()->has('member')){
            return redirect() -> route('login')->withErrors(['login_required'=> trans('view.form.login.login_required')])->withInput(['redirect' => $request->path()]);
        }

        if($request->isMethod('post')){
            //生成授权码  generate a code.
            $code = substr(md5($client->secret.time().$request->session()->get('member.username')),rand(0,19),12);

            while(!OAuthAuthCodes::where('code','=',$code)->first() == null){
                $code = substr(md5($client->secret.time().$request->session()->get('member.username')),rand(0,19),12);
            }

            $authCodes = new OAuthAuthCodes();
            $authCodes->user_id = $request->session()->get('member.userid');
            $authCodes->client_id = $clientid;
            $authCodes->code = $code;
            $authCodes->expires_at = time() + ConfigGlobalWebsite::all()->first()->oauth_auth_code_expire;

            $request->session()->forget('clientid');
            if($authCodes->save()){
                $request->session()->forget('clientid');
                $url = str_replace('{authcode}',$code,$client->redirect);
                return redirect($url);
            }else{
                return redirect()->route('OAuthIndex')->withErrors(['save_fail' => trans('view.oauth.save_fail')]);
            }
        }

        return view('oauth',['client' => $client]);
    }

    public function getOAuthAccessToken(Request $request){
        if($request->isMethod('post')){
            $authcode = $request->get('authcode');
            $secret = $request->get('secret');
            $authcode_data = OAuthAuthCodes::where('code','=',$authcode)->first();
            $client_data = OAuthClients::where('secret','=',$secret)->first();

            if($client_data == null){
                $info = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getaccesstoken.client_secret_not_found'),
                ];
                return response()->json($info);
            }

            if($authcode_data == null){
                $info = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getaccesstoken.authcode_not_found'),
                ];
                return response()->json($info);
            }
            if($client_data->id != $authcode_data->client_id){
                $info = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getaccesstoken.wrong_secret'),
                ];
                return response()->json($info);
            }
            if(time() > $authcode_data->expires_at){
                $authcode_data->delete(); //删除过期auth code    delete expire auth code.
                $info = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getaccesstoken.authcode_expired'),
                ];
                return response()->json($info);
            }

            $authcode_id = $authcode_data->id;
            $user_id = $authcode_data->user_id;
            $client_id = $authcode_data->client_id;
            $authcode_expires_at = $authcode_data->expires_at;

            //生成access token和update_token   generate a access token and update token.
            $tokens = $this->generate_token($user_id,$client_id,$authcode,$authcode_id,$authcode_expires_at);
            $access_token = $tokens['at'];
            $update_token = $tokens['ut'];

            //保存access token   save the access token.
            /*$db_accesstoken = OAuthAccessTokens::where('user_id','=',$user_id)->where('client_id','=',$client_id)->first();
            $oldtoken = null;
            if($db_accesstoken == null){
                $db_accesstoken = new OAuthAccessTokens();
            }else{
                $oldtoken = $db_accesstoken->token;
            }*/
            $result = $this->save_access_token($user_id,$client_id,$access_token);
            $expires_at = $result['expires'];
            if(!$result['status']){
                $info = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getaccesstoken.access_token_save_fail'),
                ];
                return response()->json($info);
            }

            //保存update token    save the update token.
            /*if($oldtoken != null){
                $db_updatetoken  = OAuthRefreshTokens::where('access_token_id','=',$oldtoken)->first();
                $db_updatetoken->delete();
            }*/
            $result = $this->save_update_token($access_token,$update_token,$expires_at);
            $update_token_expires_at = $result['expires'];
            if(!$result['status']){
                $info = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getaccesstoken.update_token_save_fail'),
                ];
                return response()->json($info);
            }

            $authcode_data->delete();
            $info = [
                'status' => 'success',
                'access_token' => $access_token,
                'expires_at' => $expires_at,
                'update_token' => $update_token,
                'update_token_expires_at' => $update_token_expires_at,
            ];
            return response()->json($info);

        }
    }

    public function getUserInfo(Request $request){
        $client_secret = $request->get('client_secret');
        $access_token = $request->get('access_token');
        $update_token = $request->get('update_token');
        $renewtoken = false;

        $db_oauth_clients = OAuthClients::where('secret','=',$client_secret)->first();
        if($db_oauth_clients == null){
            $return = [
                'status' => 'error',
                'details' => trans('view.oauth.api.getuserinfo.invalid_clinet_secret') ,
            ];
            return response()->json($return);
        }
        $db_access_token = OAuthAccessTokens::where('token','=',$access_token)->first();
        if($db_access_token == null){
            $return = [
                'status' => 'error',
                'details' => trans('view.oauth.api.getuserinfo.invalid_access_token') ,
            ];
            return response()->json($return);
        }

        if($db_access_token->client_id != $db_oauth_clients->id){
            $return = [
                'status' => 'error',
                'details' => trans('view.oauth.api.getuserinfo.incorrect_secret_for_access_token') ,
            ];
            return response()->json($return);
        }
        $clientid = $db_access_token->client_id;
        $userid = $db_access_token->user_id;

        if(time() > $db_access_token->expires_at){
            $db_update_token = OAuthRefreshTokens::where('token','=',$update_token)->first();
            if($db_update_token == null){
                $return = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getuserinfo.invalid_update_token') ,
                ];
                return response()->json($return);
            }

            if($db_update_token->access_token_id != $db_access_token->token){
                $return = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getuserinfo.incorrect_update_token') ,
                ];
                return response()->json($return);
            }

            if(time() > $db_update_token->expires_at){
                $return = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getuserinfo.update_token_expires') ,
                ];
                return response()->json($return);
            }

            $db_access_token->delete();
            $db_update_token->delete();
            $result = $this->generate_token($userid,$clientid,'0','0',time());
            $access_token = $result['at'];
            $update_token = $result['ut'];
            $result = $this->save_access_token($userid,$clientid,$access_token);
            $expires_at = $result['expires'];
            if(!$result['status']){
                $return = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getuserinfo.access_token_can_not_save') ,
                ];
                return response()->json($return);
            }
            $result = $this->save_update_token($access_token,$update_token,$expires_at);
            $update_token_expires_at = $result['expires'];
            if(!$result['status']){
                $return = [
                    'status' => 'error',
                    'details' => trans('view.oauth.api.getuserinfo.update_token_can_not_save') ,
                ];
                return response()->json($return);
            }
            $renewtoken = true;
        }

        $userinfo = Users::where('id','=',$userid)->first();
        $member = [
            'userid' => $userinfo->id,
            'username' => $userinfo->username,
            'group' => $userinfo->group,
            'baned' => $userinfo->baned,
            'email' => $userinfo->email,
            'email_active' => $userinfo->email_active,
            'QQ' => $userinfo->QQ,
            'QQ_active' => $userinfo->QQ_active,
            'reg_address' => $userinfo->reg_address,
            'created_at' => $userinfo->created_at,
            'updated_at' => $userinfo->updated_at,
            'last_login' => $userinfo->last_login,
        ];
        if($renewtoken){
            $token_info = [
                'status' => 'renew',
                'access_token' => $access_token,
                'expires_at' => $expires_at,
                'update_token' => $update_token,
                'update_token_expires_at' => $update_token_expires_at,
            ];
        }else{
            $token_info = [
                'status' => 'none',
            ];
        }
        $return = [
            'status' => 'success',
            'details' => $member ,
            'token' => $token_info,
        ];
        return response()->json($return);
    }

    public function getAvatar($userid){
        $db_user = Users::where('id','=',$userid)->first();
        if($db_user == null){
            return 'invalid user id.';
        }
        return redirect($db_user->avatar);
    }

    private function generate_token($user_id,$client_id,$authcode,$authcode_id,$authcode_expires_at){
        $access_token = md5(time().$user_id).crypt($client_id.$authcode_id.$authcode_expires_at,rand(10,99));
        while(OAuthAccessTokens::where('token','=',$access_token)->where('client_id','=',$client_id)->first() != null){
            $access_token = md5(time().$user_id).crypt($client_id.$authcode_id.$authcode_expires_at,rand(10,99));
        }
        $update_token = md5($authcode.time()).crypt($access_token.time(),rand(10,99));
        while(OAuthRefreshTokens::where('token','=',$update_token)->first() != null){
            $update_token = md5($authcode.time()).crypt($access_token.time(),rand(10,99));
        }
        return [
            'at' => $access_token,
            'ut' => $update_token,
        ];
    }

    private function save_access_token($user_id,$client_id,$access_token){
        $db_accesstoken = new OAuthAccessTokens();
        $db_accesstoken->user_id = $user_id;
        $db_accesstoken->client_id = $client_id;
        $db_accesstoken->token = $access_token;
        $expires_at = time() + ConfigGlobalWebsite::all()->first()->oauth_access_token_expire;
        $db_accesstoken->expires_at = $expires_at;
        $result = $db_accesstoken->save();
        return [
            'status' => $result,
            'expires' => $expires_at,
        ];
    }

    private function save_update_token($access_token,$update_token,$expires_at){
        $db_updatetoken = new OAuthRefreshTokens();
        $db_updatetoken->access_token_id = $access_token;
        $db_updatetoken->token = $update_token;
        $update_token_expires_at = $expires_at + ConfigGlobalWebsite::all()->first()->oauth_update_token_expire;
        $db_updatetoken->expires_at =$update_token_expires_at;
        $result =$db_updatetoken->save();
        return [
            'status' => $result,
            'expires' => $update_token_expires_at,
        ];
    }
}