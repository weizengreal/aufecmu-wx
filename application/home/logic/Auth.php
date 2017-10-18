<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/30
 * Time: 下午5:03
 */

namespace app\home\logic;
use app\home\model\Xs;
use app\home\model\Vipuser;
use wxLib\Card;
use think\Log;
use interfaceLib\wxManager;

class Auth {
    private $xs;
    private $card;
    private $vipuser;

    public function __construct() {
        $this->xs = new  Xs();
        $this->card = new Card();
        $this->vipuser = new Vipuser();
    }

    /*
     * 用户激活会员卡使用非静默授权，此时需要更新用户的所有数据
     * 初始化用户的数据
     * */
//    public function updateUserInfo($scopeData) {
//        $card = new \wxLib\Card();
//        $wxUserInfo=json_decode($card->getWxUserInfo($scopeData['openid']),true);
//        if( !empty($scopeData['openid']) && !$this->isa->isExist($scopeData['openid'])) {
//            $upData = array(
//                'openid'=>$scopeData['openid'],
//                'addtime'=>time(),
//                'uptime'=>time(),
//                'status'=>2
//            );
//            if( !empty($wxUserInfo['subscribe']) && $wxUserInfo['subscribe'] == 1) {
//                $upData['unionid']=$wxUserInfo['unionid'];
//                $upData['cname']=$wxUserInfo['nickname'];
//                $upData['headimgurl']=$wxUserInfo['headimgurl'];
//            }
//            return $this->isa->addNew($upData);
//        }
//        else {
//            $upData = array(
//                'uptime'=>time(),
//            );
//            if($wxUserInfo['subscribe'] == 1) {
//                $upData['headimgurl']=$wxUserInfo['headimgurl'];
//            }
//            return $this->isa->updata($scopeData['openid'],$upData);
//        }
//    }

    /*
     * 激活内部逻辑
     * */
    public function active($postData) {
//        return ['status'=>-2,'info'=>'您的账户丢失在遥远的二次元空间，正在为您跳转到自定义注册','url'=>url("home/index/active_detail",array('xm'=>$postData['xm'],'sfz'=>$postData['sfz']),false,true) ];
        if(session("?openid")) {
            $openid = session("openid");
            if($this->vipuser->isExist(array('openid'=>$openid))) {
                if($this->xs->isExist(
                    array(
                        'name'=>$postData['xm'],
                        'sfz'=>substr($postData['sfz'],-6)
                    )
                )) {
                    $studentData = $this->xs->getOne(array(
                        'name'=>$postData['xm'],
                        'sfz'=>substr($postData['sfz'],-6)
                    ));
                    $day=getDays($studentData['grade'])."天";
                    $result = $this->vipuser->getInfoByArr(array('openid'=>$openid));
                    $res=$this->card->setXyqActive($result['code'],$studentData['studentid'],$studentData['grade'],$studentData['name'],$day);
                    if($res['errcode'] == 1) {
                        // 想不到什么失败的情况，所以直接酱紫写了
                        $this->vipuser->updata($openid,array(
                            'studentid'=>$studentData['studentid'],
                            'name'=>$postData['xm'],
                            'addtime'=>time(),
                        ));
                        $manager = new wxManager();
                        $manager->setUserLabel([$openid],$manager->getTagId($studentData['grade']));
                        return ['status'=>1,'info'=>'ok','url'=>url("home/message/msg_success",array('title'=>'激活成功','content'=>'恭喜您'.$studentData['name']."，您已经成功激活了安财校友卡！",'redirectUri'=>config("XYQURL")),false,true)];
                    }
                    else {
                        Log::write(json_encode($res),"active");
                        return ['status'=>-6,'info'=>'内部出错，出错位置101'];
                    }
                }
                else {
                    return ['status'=>-2,'info'=>'您的账户丢失在遥远的二次元空间，正在为您跳转到自定义注册','url'=>url("home/index/active_detail",array('xm'=>$postData['xm'],'sfz'=>$postData['sfz']),false,true) ];
                }
            }
            else {
                return ['status'=>-3,'info'=>'内部错误  '.$openid];
            }
        }
        else {
            return ['status'=>-4,'info'=>'cookie超时，请关闭页面重试！'];
        }
    }

    /*
     * 正确的身份证，数据库无记录
     * */
    public function active_detail($postData) {
        if(session("?openid")) {
            $openid = session("openid");
            if($this->vipuser->isExist(array('openid'=>$openid))) {
                $grade=substr($postData['studentid'],0,4);
                $day=getDays($grade)."天";
                $result = $this->vipuser->getInfoByArr(array('openid'=>$openid));
                $res=$this->card->setXyqActive($result['code'],$postData['studentid'],$grade,$postData['xm'],$day);
                if($res['errcode'] == 1) {
                    $this->vipuser->updata($openid,array(
                        'studentid'=>$postData['studentid'],
                        'name'=>$postData['xm'],
                        'addtime'=>time(),
                    ));
                    $manager = new wxManager();
                    $manager->setUserLabel([$openid],$manager->getTagId($grade));
                    return ['status'=>1,'info'=>'ok','url'=>url("home/message/msg_success",array('title'=>'激活成功','content'=>'恭喜您'.$postData['xm']."，您已经成功激活了安财校友卡！",'redirectUri'=>config("XYQURL")),false,true)];
                }
                else {
                    Log::write(json_encode($res),"active_detail");
                    return ['status'=>-6,'info'=>'内部出错，出错位置101'];
                }
            }
            else {
                return ['status'=>-3,'info'=>'cookie超时，请关闭页面重试！'];
            }
        }
        else {
            return ['status'=>-4,'info'=>'cookie超时，请关闭页面重试！'];
        }
    }

}

