<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/31
 * Time: 下午9:32
 */

namespace app\home\controller;
use app\home\model\Vipuser;
use think\Controller;
use think\Request;
use app\home\logic\Login;
use app\home\logic\Card;

class Xyq extends Controller {

    public function rank() {
        $wxScope = new \wxLib\Scope();
        $login = new Login();
        $code = Request::instance()->get("code");
        $scopeData = $wxScope->getUnionid($code);
        if(empty($scopeData['unionid'])) {
            $this->redirect(url("home/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg']  )));
        }
        $scopeData['code'] = $code;
        $res = $login->login($scopeData,true);
        if($res['status']) {
            $card = new Card();
            return $this->fetch("rank",[
                "share"=>url("home/index/share","",false),
                "cardId"=>config("cardId"),
                "thisLink" => getWxAuthUrl("",url("home/Xyq/rank","",false,true),"1"),
                "personInfo" => $card->getOne($scopeData['openid']),
                "rank"=>$card->getRank($scopeData['openid']),
                'access_token'=>$res['access_token'],
                'timeOut'=>$res['timeOut'],
                'headimgurl'=>$res['headimgurl'],
                'nickname'=>$res['nickname'],
            ]);
        }
        else{
            $this->redirect(url("home/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>getWxAuthUrl(null,url("home/xyq/rank","",false,true),"" ) )));
        }
    }

    public function cardget() {
        $wxScope = new \wxLib\Scope();
        $scopeData = $wxScope->getToken(Request::instance()->get("code"));
        if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
            $vip = new \app\home\model\Vipuser();
            $selfbg = "";
            $selfbgauthor = "";
            $res = $vip->getInfoByArr(array('openid'=>$scopeData['openid']));
            if( !empty($res)) {
                $selfbg = (string)$res['bg'];
                $allBgArr = json_decode(file_get_contents("./public/static/bg.json"),true);
                foreach ($allBgArr as $index => $item) {
                    if( strpos($item["img"],$selfbg) !== false ) {
                        $selfbg=$item["img"];
                        $selfbgauthor=$item["author"];
                        unset($allBgArr[$index]);
                    }
                }
                return $this->fetch("cardget",[
                    'selfbg'=>$selfbg,
                    'selfbgauthor'=>$selfbgauthor,
                    'allbg'=>$allBgArr,
                    "share"=>url("home/index/share","",false),
                    'changeBg'=>url("home/xyq/changeBg","",false),
                    "thisLink" => getWxAuthUrl("base",url("home/Xyq/cardget","",false,true),"1")
                ]);
            }
            else {
                $this->redirect(url("home/message/msg_error",array('title' => '会员卡错误' , 'content'=>'请重新领取会员卡再试或者直接联系管理员！' )));
            }
        }
        else {
            $this->redirect(url("home/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>urlencode(getWxAuthUrl(null,url("home/index/active","",false,true),"" )) )));
        }
    }

    public function changeBg() {
        $postData = Request::instance()->post();
        if(!empty($postData['index'])) {
            if(session("?openid")) {
                $openid=session("openid");
                $allBgArr = json_decode(file_get_contents("./public/static/bg.json"),true);
//                dump($postData);
                foreach ($allBgArr as $index => $item) {
                    if(  $item["index"] == $postData['index'] ) {
                        //找到该背景图片，可以替换，为了省时间，直接在这里面写了
//                        dump($postData['index']);
                        $vip = new \app\home\model\Vipuser() ;
                        $card = new \wxLib\Card();
                        $res = $vip->getInfoForChangeBg($openid) ;
//                        dump($res);
                        if(!empty($res['openid'])) {
                            //更新数据库、再更新卡片图片背景
                            $grand = substr($res['studentid'],0,4);
                            $day=getDays($grand)."天";
                            if($vip->updata($openid,array("bg"=>$item["index"]))  &&  $card->setXyqActiveForChangeBg($res['code'],$res['studentid'],$grand,$res['name'],$day,$item["bg"]) ) {
                                return ['status'=>1,'info'=>"ok"];
                            }
                            else {
                                return ['status'=>-5,'info'=>"inner error"];
                            }
                        }
                        else {
                            return ['status'=>-4,'info'=>"your data lose ! enhen~"];
                        }
                    }
                }
                return ['status'=>-3,'info'=>"diff"];
            }
            else {
                return ['status'=>-2,'info'=>"your cookie may too long"];
            }
        }
        else {
            return ['status'=>-1,'info'=>"lose params"];
        }
    }

    public function getRank() {
        if(isset($_GET['page'])) {
            $page = Request::instance()->get("page",1);
            $start = ($page-1)*20;
            $card = new Card();
            return [
                'status'=>1,
                'info'=>"ok",
                'data'=>$card->getPageRank($start)
            ];
        }
        else {
            return [
                'status'=>-1,
                'info'=>'lose params!',
                'data'=>null
            ];
        }
    }

    public function _rank() {
//        dump(json_decode('[{"cou":7}]'));die;
        $wxScope = new \wxLib\Scope();
        $scopeData = $wxScope->getToken(Request::instance()->get("code"));
        if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
            if(!empty($_GET['state']) && $_GET['state']=="newuser") {
                return $this->fetch("rank",[
                    "share"=>url("home/index/share","",false),
                    "cardId"=>config("cardId"),
                    "isGet"=>3,
                    "rank"=>"-1",
                    "thisLink" => getWxAuthUrl("base",url("home/Xyq/rank","",false,true),"1")
                ]);
            }
            $vip = new \app\home\model\Vipuser();
            if($vip->isExist(array(
                'openid'=>$scopeData['openid'],
            ))) {
                //已经领取到会员卡（这个判断是不合乎逻辑的，需要判定下code状态）
                return $this->fetch("rank",[
                    "share"=>url("home/index/share","",false),
                    "cardId"=>config("cardId"),
                    "isGet"=>1,
                    "rank"=>-1,
                    "thisLink" => getWxAuthUrl("base",url("home/Xyq/rank","",false,true),"1")
                ]);
            }
            else {
                //未领取会员卡
                return $this->fetch("rank",[
                    "share"=>url("home/index/share","",false),
                    "cardId"=>config("cardId"),
                    "isGet"=>2,
                    "rank"=>"-1",
                    "thisLink" => getWxAuthUrl("base",url("home/Xyq/rank","",false,true),"1")
                ]);
            }
        }
        else{
            $this->redirect(url("home/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>getWxAuthUrl(null,url("home/xyq/rank","",false,true),"" ) )));
        }
    }


}


