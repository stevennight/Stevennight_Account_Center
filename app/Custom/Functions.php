<?php
namespace App\Custom;

use App\Database\ConfigGlobalWebsite;
use App\Database\Links;
use App\Database\Users;
use Illuminate\Http\Request;

/**自定义全局函数
 * Custom global function
 *
 * Class Functions
 * @package App\Custom
 */
class Functions{

    //从数据库中获取数据并刷新用户信息    get data of user information who is login from database and refresh the session.
    public static function refressUserinfoSession(Request $request,$userid){
        $userinfo = Users::where('id','=',$userid)->first();
        if($userinfo == null){
            return false;
        }
        //更新session 的用户信息   renew the information of member

        $member = [
            'userid' => $userinfo->id,
            'username' => $userinfo->username,
            'group' => $userinfo->group,
            'baned' => $userinfo->baned,
            'avatar' => $userinfo->avatar,
            'email' => $userinfo->email,
            'email_active' => $userinfo->email_active,
            'QQ' => $userinfo->QQ,
            'QQ_active' => $userinfo->QQ_active,
            'reg_address' => $userinfo->reg_address,
            'created_at' => $userinfo->created_at,
            'updated_at' => $userinfo->updated_at,
            'last_login' => $userinfo->last_login,
        ];
        $request->session()->put('member',$member);
        return true;
    }

    public static function getConfigGlobalWebsite(){
        try{
            $config = ConfigGlobalWebsite::all()->first();
        }catch(\Exception $e){
            return false;
        }
        if($config == null){
            return false;
        }
        session()->put('config',$config);

        $db_links = Links::all();
        session()->put('links',$db_links);
        return true;
    }

}