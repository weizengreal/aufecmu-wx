<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/31
 * Time: 下午9:32
 */

namespace app\index\controller;
use think\Controller;
use think\Request;

class Xyq extends Controller {

    public function rank() {
//        dump(json_decode('[{"cou":7}]'));die;
        $wxScope = new \wxLib\Scope();
        $scopeData = $wxScope->getUnionid(Request::instance()->get("code"));
        if( !empty($scopeData['openid']) ) {
            $isa = new \app\index\model\Isa();
            $isa->updata($scopeData['openid'],array(
                'headimgurl'=>$scopeData['headimgurl']
            ));
            $openid=$scopeData['openid'];
            $resultCount = $isa->where("code is not null and code != '' and `openid`='$openid'")->count();
            if($resultCount > 0) {
                return $this->fetch("rank",[
                    "share"=>url("Index/index/share","",false),
                    "cardId"=>config("cardId"),
                    "thisLink" => getWxAuthUrl("",url("Index/Xyq/rank","",false,true),"1"),
                    "personInfo" => $isa->getOneRank($scopeData['openid']),
                    "rank"=>$isa->getRank($scopeData['openid'])
                ]);
            }
            else {
                $this->redirect(url("index/index/active","",false));
            }
        }
        else{
            $this->redirect(url("index/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>getWxAuthUrl(null,url("index/xyq/rank","",false,true),"" ) )));
        }
    }

    public function cardget() {
        $wxScope = new \wxLib\Scope();
        $scopeData = $wxScope->getToken(Request::instance()->get("code"));
        if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
            $vip = new \app\index\model\Vipuser();
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
                    "share"=>url("Index/index/share","",false),
                    'changeBg'=>url("Index/xyq/changeBg","",false),
                    "thisLink" => getWxAuthUrl("base",url("Index/Xyq/cardget","",false,true),"1")
                ]);
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '会员卡错误' , 'content'=>'请重新领取会员卡再试或者直接联系管理员！' )));
            }
        }
        else {
            $this->redirect(url("index/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>urlencode(getWxAuthUrl(null,url("index/index/active","",false,true),"" )) )));
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
                        $vip = new \app\index\model\Vipuser() ;
                        $isa = new \app\index\model\Isa() ;
                        $card = new \wxLib\Card();
                        $res = $isa->getInfoForChangeBg($openid) ;
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
            $isa = new \app\index\model\Isa();
            $res = $isa->getPageRank($start);
            for($i=0;$i<count($res);++$i) {
                $res[$i]['rank']=$start+$i+1;
                $res[$i]['headImgUrl'] = empty($res[$i]['headImgUrl']) ?
                    "http://wx.aufe.vip/aufecmu_v4/public/images/headimg/".rand(1,20).".jpg"
                    : $res[$i]['headImgUrl'];
            }
            $result = $res;
            echo json_encode([
                'status'=>1,
                'info'=>"ok",
                'data'=>$result //TODO::update
            ],JSON_UNESCAPED_UNICODE);die;
            return [
                'status'=>1,
                'info'=>"ok",
                'data'=>$result
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

    public function test() {
        return $this->fetch("test",[
            "share"=>url("Index/index/share","",false),
            "thisLink" => getWxAuthUrl("base",url("Index/Xyq/cardget","",false,true),"1")
        ]);
    }

    public function _rank() {
//        dump(json_decode('[{"cou":7}]'));die;
        $wxScope = new \wxLib\Scope();
        $scopeData = $wxScope->getToken(Request::instance()->get("code"));
        if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
            if(!empty($_GET['state']) && $_GET['state']=="newuser") {
                return $this->fetch("rank",[
                    "share"=>url("Index/index/share","",false),
                    "cardId"=>config("cardId"),
                    "isGet"=>3,
                    "rank"=>"-1",
                    "thisLink" => getWxAuthUrl("base",url("Index/Xyq/rank","",false,true),"1")
                ]);
            }
            $vip = new \app\index\model\Vipuser();
            if($vip->isExist(array(
                'openid'=>$scopeData['openid'],
            ))) {
                //已经领取到会员卡（这个判断是不合乎逻辑的，需要判定下code状态）
                return $this->fetch("rank",[
                    "share"=>url("Index/index/share","",false),
                    "cardId"=>config("cardId"),
                    "isGet"=>1,
                    "rank"=>-1,
                    "thisLink" => getWxAuthUrl("base",url("Index/Xyq/rank","",false,true),"1")
                ]);
            }
            else {
                //未领取会员卡
                return $this->fetch("rank",[
                    "share"=>url("Index/index/share","",false),
                    "cardId"=>config("cardId"),
                    "isGet"=>2,
                    "rank"=>"-1",
                    "thisLink" => getWxAuthUrl("base",url("Index/Xyq/rank","",false,true),"1")
                ]);
            }
        }
        else{
            $this->redirect(url("index/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>getWxAuthUrl(null,url("index/xyq/rank","",false,true),"" ) )));
        }
    }


}


