<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/30
 * Time: 下午5:03
 */

namespace app\index\logic;
use app\index\model\Isa;
use app\index\model\Xs;
use app\index\model\Vipuser;
use wxLib\Card;

class Auth {
    private $isa;
    private $xs;
    private $card;
    private $vipuser;

    public function __construct() {
        $this->isa = new  Isa();
        $this->xs = new  Xs();
        $this->card = new Card();
        $this->vipuser = new Vipuser();
    }

    /*
     * 用户激活会员卡使用非静默授权，此时需要更新用户的所有数据
     * 初始化用户的数据
     * */
    public function updateUserInfo($scopeData) {
        $card = new \wxLib\Card();
        $wxUserInfo=(!empty($scopeData['headimgurl']) && !empty($scopeData['nickname']) ) ? $scopeData : json_decode($card->getWxUserInfo($scopeData['openid']),true);
        if( !empty($scopeData['openid']) && !$this->isa->isExist($scopeData['openid'])) {
            $upData = array(
                'openid'=>$scopeData['openid'],
                'addtime'=>time(),
                'uptime'=>time(),
                'status'=>2
            );
            if( !empty($wxUserInfo['subscribe']) && $wxUserInfo['subscribe'] == 1) {
                $upData['unionid']=$wxUserInfo['unionid'];
                $upData['cname']=$wxUserInfo['nickname'];
                $upData['headimgurl']=$wxUserInfo['headimgurl'];
            }
            return $this->isa->addNew($upData);
        }
        else {
            $upData = array(
                'uptime'=>time(),
            );
            if($wxUserInfo['subscribe'] == 1) {
                $upData['headimgurl']=$wxUserInfo['headimgurl'];
            }
            return $this->isa->updata($scopeData['openid'],$upData);
        }
    }

    /*
     * 激活内部逻辑
     * 进行到这里了
     *
     * */
    public function active($postData) {
        if(session("?openid") && $this->vipuser->isExist(array('openid'=>session("openid"))) ) {
            $result = $this->vipuser->getInfoByArr(array('openid'=>session("openid")));
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
                $res=$this->card->setXyqActive($result['code'],$studentData['studentid'],$studentData['grade'],$studentData['name'],$day);
                if($res['errcode'] == 1) {
                    $access_token=password_hash(session("access_token"),PASSWORD_BCRYPT);
                    if($this->setActiveStatus($studentData['studentid'],$res['code'],config("cardId"),$access_token)) {
                        return ['status'=>1,'info'=>'ok','url'=>url("index/message/msg_success",array('title'=>'激活成功','content'=>'恭喜您'.$studentData['name']."，您已经成功激活了安财校友卡！"),false,true)];
                    }
                    else {
                        return ['status'=>-6,'info'=>'内部出错，出错位置601'];
                    }
                }
                else {
//                    \think\Log::write(json_encode($res),"WARNING123456");
                    return ['status'=>-5,'info'=>'激活出现错误，请重试！'];
                }
            }
            else {
                if($this->xs->isExist(
                    array(
                        'sfz'=>substr($postData['sfz'],-6)
                    )
                )) {
                    return ['status'=>-3,'info'=>'请填写入学时的姓名'];
                }
                else {
                    return ['status'=>-2,'info'=>'您的账户丢失在遥远的二次元空间，正在为您跳转到自定义注册','url'=>url("index/index/active_detail",array('xm'=>$postData['xm'],'sfz'=>$postData['sfz']),false,true) ];
                }
            }
        }
        else {
            return ['status'=>-4,'cookie超时，请关闭页面重试！'];
        }

//        if(isset($_COOKIE['encrypt_code'])) {
//            $encrypt_code=str_replace(" ","+", $_COOKIE['encrypt_code']) ;
////            file_put_contents("test.txt",$encrypt_code.PHP_EOL,FILE_APPEND);
//
//        }
//        else {
//            return ['status'=>-4,'cookie超时，请关闭页面重试！'];
//        }
    }

    /*
     * 正确的身份证，数据库无记录
     * */
    public function active_detail($postData) {
        if(session("?openid") && $this->vipuser->isExist(array('openid'=>session("openid"))) ) {
            $result = $this->vipuser->getInfoByArr(array('openid'=>session("openid")));
            if(!$this->xs->isExist(array('studentid'=>$postData['studentid']))) {

                $grade=substr($postData['studentid'],0,4);
                $day=getDays($grade)."天";
                $res=$this->card->setXyqActive($result['code'],$postData['studentid'],$grade,$postData['xm'],$day);

                if($res['errcode'] == 1) {
                    $access_token=password_hash(session("access_token"),PASSWORD_BCRYPT);
                    if($this->setActiveStatus_detail($postData,$res['code'],config("cardId"),$access_token)) {
                        return ['status'=>1,'info'=>'ok','url'=>url("index/message/msg_success",array('title'=>'激活成功','content'=>'恭喜您'.$postData['xm']."，您已经成功激活了安财校友卡！"),false,true)];
                    }
                    else {
                        return ['status'=>-6,'info'=>'内部出错，出错位置602'];
                    }
                }
                else {
                    return ['status'=>-5,'info'=>'激活出现错误，请重试！'];
                }
            }
            else {
                if($this->xs->isExist(
                    array(
                        'studentid'=>$postData['studentid'],
                        'sfz'=>substr($postData['sfz'],-6)
                    )
                )) {
                    $studentData = $this->xs->getOne(array(
                        'studentid'=>$postData['studentid'],
                    ));
                    $day=getDays($studentData['grade'])."天";
                    $res=$this->card->setXyqActive($result['code'],$studentData['studentid'],$studentData['grade'],$studentData['name'],$day);
                    if($res['errcode'] == 1) {
                        $access_token=password_hash(session("access_token"),PASSWORD_BCRYPT);
                        if($this->setActiveStatus($studentData['studentid'],$res['code'],config("cardId"),$access_token)) {
                            return ['status'=>1,'info'=>'ok','url'=>url("index/message/msg_success",array('title'=>'激活成功','content'=>'恭喜您'.$studentData['name']."，您已经成功激活了安财校友卡！"),false,true)];
                        }
                        else {
//                            \think\Log::write(json_encode($res),"WARNING123456");
                            return ['status'=>-6,'info'=>'内部出错，出错位置603'];
                        }
                    }
                    else {
                        return ['status'=>-5,'info'=>'激活出现错误，请重试！'];
                    }
                }
                else {

                    return ['status'=>2,'info'=>'系统检测到您的学号和身份证不对应，请使用您的真实身份！'];
                }
            }
        }
        else {
            return ['status'=>-4,'cookie超时，请关闭页面重试！'];
        }

//        if(isset($_COOKIE['encrypt_code'])) {
//            $encrypt_code=str_replace(" ","+", $_COOKIE['encrypt_code']) ;
//            file_put_contents("test.txt",$encrypt_code.PHP_EOL,FILE_APPEND);
//
//        }
//        else {
//            return ['status'=>-4,'cookie超时，请关闭页面重试！'];
//        }
    }

    /*
     * 传入学号将该用户的基本信息设置为已激活
     * 1、设置xs表该学号为已使用
     * 2、设置Isa表status为1,写入studentid、code和cardid
     * */
    private function setActiveStatus($studentid,$code,$cardId,$access_token) {
        $openid=session("openid");
        return $this->isa->updata($openid,array(
                'studentid'=>$studentid,
                'cardid'=>$cardId,
                'code'=>$code,
                'status'=>1,
                'access_token'=>$access_token,
                'uptime'=>time()
            )) !== false  && $this->xs->where(array('studentid'=>$studentid))->setField("status","2") !== false;
    }

    /*
         * 传入学号将该用户的基本信息设置为已激活
         * 1、设置xs表该学号为已使用
         * 2、设置Isa表status为1,写入studentid、code和cardid
         * */
    private function setActiveStatus_detail($postData,$code,$cardId,$access_token) {
        $openid=session("openid");
        $postData['name']=$postData['xm'];
        $postData['status']=3;
        unset($postData['xm']);
        unset($postData['__token__']);
        return $this->isa->updata($openid,array(
                'studentid'=>$postData['studentid'],
                'cardid'=>$cardId,
                'code'=>$code,
                'status'=>1,
                'access_token'=>$access_token,
                'addtime'=>time()
            )) && $this->xs->addNew($postData);
    }




}

