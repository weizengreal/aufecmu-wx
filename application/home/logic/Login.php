<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/5/13
 * Time: 下午11:43
 */

namespace app\home\logic;
use \app\home\model\User;

class Login {

    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    // 判定数据库当前用户的权限是否过期，过期更新
//    public function auth($userArr,$retCode = 1) {
//        $authJudge = \think\Db::table("club_user")->where("`unionid` = '{$userArr['unionid']}' and `token_time` > ".time())->find();
//        if($retCode == 1) {
//            return empty($authJudge) ? $this->login($userArr,2) : $this->login($userArr);
//        }
//        else {
//
//        }
//    }

    /*
     * 用户登录接口
     * userArr属于用户通过授权登录获取的基本信息
     * retCode表示是否需要返回基本用户的头像、昵称等基本信息
     *
     * */
    public function login($userArr,$retCode = false) {
        if( empty($userArr['openid']) ) {
            return false;
        }
        $time = time();
        $isaArr = [
            'headimgurl' => $userArr['headimgurl'],
            'nickname' => $userArr['nickname'],
            'uptime' => $time,
            'token_time'=>$time+3600*24*3,
            'status' =>1
        ];
        // 判断该用户是否存在
        if($retCode === false) {
            if($this->user->isExist([
                'unionid' => $userArr['unionid']
            ])) {
                //查看当前用户的权限是否到期
                $accessToken=$this->user->getAccessToken($userArr['unionid']);
                if(empty($accessToken)) {//权限过期
                    $isaArr['access_token'] = $this->getOneAccessToken($userArr['code']);
                }
                return $this->user->updata($userArr['unionid'],$isaArr) ;
            }
            else {
                $isaArr += [
                    'openid' => $userArr['openid'],
                    'unionid' => $userArr['unionid'],
                    'access_token' => $this->getOneAccessToken($userArr['code']),
                ];
                return $this->user->addNew($isaArr);
            }
        }
        else {
            if($this->user->isExist([
                'unionid' => $userArr['unionid']
            ])) {
                $accessToken=$this->user->getAccessToken($userArr['unionid']);
                if(empty($accessToken)) {
                    $isaArr['access_token'] = $this->getOneAccessToken($userArr['code']);
                }
                return [
                    'status'=>$this->user->updata($userArr['unionid'],$isaArr),
                    'access_token'=>empty($isaArr['access_token']) ? $accessToken : $isaArr['access_token'],
                    'timeOut'=>$isaArr['token_time'],
                    'headimgurl'=>$isaArr['headimgurl'],
                    'nickname'=>$isaArr['nickname'],
                ] ;
            }
            else {
                $isaArr += [
                    'openid' => $userArr['openid'],
                    'unionid' => $userArr['unionid'],
                    'access_token' => $this->getOneAccessToken($userArr['code']),
                ];
                return [
                    'status'=>$this->user->addNew($isaArr),
                    'access_token'=>$isaArr['access_token'],
                    'timeOut'=>$isaArr['token_time'],
                    'headimgurl'=>$isaArr['headimgurl'],
                    'nickname'=>$isaArr['nickname'],
                ] ;
            }
        }
    }


    public function getOneAccessToken($singleId) {
        $accessToken = password_hash($singleId,PASSWORD_BCRYPT);
        if($this->user->isExist(array('access_token'=>$accessToken))) {
            $accessToken = password_hash($singleId,PASSWORD_BCRYPT);
        }
        return $accessToken;
    }

}

