<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/31
 * Time: 上午10:46
 */

namespace app\index\controller;
use think\Controller;

class Cardinterface extends Controller {
    /*
     SA团队服务号自定义URL导航接口部分 user_gifting_card

    {
  "touser":"OPENID",
  "msgtype":"wxcard",
  "wxcard":{
           "card_id":"123dsdajkasd231jhksad"
            },
}

    */
    public function index() {
        $wxResponse = new \wxLib\Response();
        $Dyh=new \app\index\logic\Dyh();
        $info=$wxResponse->chatBackForInterface();
//        file_put_contents("test.txt",json_encode("testindex").PHP_EOL,FILE_APPEND);die();
//        $wxResponse->backText($info->FromUserName,$info->ToUserName,"我是SA团队的弟弟SB~");die();
        if ("o16hwwb9HjRJ9uxDHWqd4FoHdeFI" == $info->FromUserName) {
            \think\Log::write(json_encode($info),"o16hwwb9HjRJ9uxDHWqd4FoHdeFI");
        }
        if( $info->MsgType == "event" && $info->Event == "user_get_card") {
            $Dyh->userGetCard($info->FromUserName, $info->UserCardCode, $info->CardId);
            if ("o16hwwb9HjRJ9uxDHWqd4FoHdeFI" == $info->FromUserName) {
//                file_put_contents("test.txt","there get card2".PHP_EOL,FILE_APPEND);
                //用户领卡行为，这里需要将用户的会员卡号和相关信息记录下来
                $wxResponse->backText($info->FromUserName, $info->ToUserName, "用户领取会员卡，OldUserCardCode：" . $info->OldUserCardCode . "   UserCardCode：" . $info->UserCardCode . "  对应的cardId为：" . $info->CardId);

//                echo "";
//                die;
            }
        }
        else if($info->MsgType == "event" && $info->Event == "user_gifting_card") {
            //转赠事件推送
            //转增事件获得，应删除相应人员的会员卡记录
//            if("o16hwwb9HjRJ9uxDHWqd4FoHdeFI"==$info->FromUserName) {
//                $Dyh->userGiftCard($info->FromUserName,$info->UserCardCode);
//
//            }
            $Dyh->userGiftCard($info->FromUserName,$info->UserCardCode);
            // file_put_contents("test.txt", "转增会员卡code：".$info->UserCardCode."  对应的cardId为：".$info->CardId);
            // $wxResponse->backText($info->FromUserName,$info->ToUserName,"转增会员卡code：".$info->UserCardCode."  对应的cardId为：".$info->CardId);
        }
        else if($info->MsgType == "event" && $info->Event == "user_view_card") {
//            $wxResponse->backText($info->FromUserName,$info->ToUserName,"进入会员卡，卡号为：".$info->UserCardCode."  对应的cardId为：".$info->CardId);
            // 测试阶段解决inner error的问题
            $vipuser = new  \app\index\model\Vipuser();
            $isa = new  \app\index\model\Isa();
            $isa->updata($info->FromUserName,array("code"=>$info->UserCardCode));
            $vipuser->updata($info->FromUserName,array("code"=>$info->UserCardCode));
//            if("o16hwwb9HjRJ9uxDHWqd4FoHdeFI"==$info->FromUserName ) {
//
////            $resStr=D("Ykt","Logic")->autoUp(array('openid'=>$info->FromUserName));//更新该用户的校园卡余额
////            file_put_contents("1.txt","InterfaController--index--info:".$info->FromUserName."----".$resStr.PHP_EOL,FILE_APPEND);
////            echo "";die;
//                $wxResponse->backText($info->FromUserName,$info->ToUserName,"进入会员卡，卡号为：".$info->UserCardCode."  对应的cardId为：".$info->CardId);
//            }
        }
        else if($info->MsgType == "event" && $info->Event == "subscribe") {
            $Dyh->userSubscribe($info->FromUserName);
//            $wxResponse->backText($info->FromUserName,$info->ToUserName,"我们身处各行各业，\n我们分布世界各地，\n我们是彼此最坚实的后盾，\n欢迎来到安财人的聚集地。");
            $wxResponse->backTw($info->FromUserName,$info->ToUserName,"欢迎来到安财人的聚集地！","我们身处各行各业，我们分布世界各地，我们是彼此最坚实的后盾。","http://wx.aufe.vip/aufecmu/index.php?s=Home/Person");
//            if("o16hwwb9HjRJ9uxDHWqd4FoHdeFI"==$info->FromUserName) {
//                $Dyh->userSubscribe($info->FromUserName);
//                $wxResponse->backText($info->FromUserName,$info->ToUserName,"我们身处各行各业，\n我们分布世界各地，\n我们是彼此最坚实的后盾。");
//            }
        }
        else if($info->MsgType == "event" && $info->Event == "TEMPLATESENDJOBFINISH") {
            if($info->Status != "success") {
                \Think\Log::write("openid:".$info->FromUserName."  Status:".$info->Status,"messageError:");
            }
        }
        else if($info->MsgType == "image") {
            $url="http://wx.aufe.vip/aufecmu/index.php?s=Home/Person";
            $wxResponse->backText($info->FromUserName,$info->ToUserName,"<a href='$url'>一张图说说你在哪里----安财校友圈</a>");
        }
//        else if($info->MsgType == "event" && $info->Event == "SCAN") {
//            $wxResponse->backText($info->FromUserName,$info->ToUserName,"只能截取到，不能干掉");
//        }
        else{
            echo "";
            die;
            if("o16hwwb9HjRJ9uxDHWqd4FoHdeFI"==$info->FromUserName) {
                $wxResponse->backText($info->FromUserName,$info->ToUserName,"我是SA团队的弟弟SB~1111");
            }
            die;
            if( $info->Content == "test" ) {
                $test=$Dyh->userSubscribe($info->FromUserName);
                $wxResponse->test($info->FromUserName,$info->ToUserName,"我是SA团队的弟弟SB，感谢你的关注~".$test);
            }
            else {
                $wxResponse->test($info->FromUserName,$info->ToUserName,"我是SA团队的弟弟SB~");
            }
        }
    }

    public function weixiao() {
        $wxResponse = new \wxLib\Response();
        $info=$wxResponse->chatBackForInterface();
        if($info->MsgType == "text" && $info->Content == "领卡") {
            $card = new \wxLib\Card();
            $card->sendKaquan($info->FromUserName);
            echo "";
            die;
        }
    }

    //TODO:interface for xyq version2.0

    public function test() {
        echo ";";
    }
}


