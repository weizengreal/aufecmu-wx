<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/31
 * Time: 上午10:46
 */

namespace app\home\controller;
use think\Controller;

class Cardinterface extends Controller {


    /*
     * 逻辑上必须处理用户领卡事件
     * TODO 1：用户关注时为用户注册相关状态信息  2：用户转赠会员卡  （非必须）
     * */
    public function index() {
        $wxResponse = new \wxLib\Response();
        $Dyh=new \app\home\logic\Dyh();
        $info=$wxResponse->chatBackForInterface();
        if( $info->MsgType == "event" && $info->Event == "user_get_card") {
            $Dyh->userGetCard($info->FromUserName, $info->UserCardCode, $info->CardId);
            if ("o16hwwb9HjRJ9uxDHWqd4FoHdeFI" == $info->FromUserName) {
                $wxResponse->backText($info->FromUserName, $info->ToUserName, "用户领取会员卡，OldUserCardCode：" . $info->OldUserCardCode . "   UserCardCode：" . $info->UserCardCode . "  对应的cardId为：" . $info->CardId);
            }
        }
//        else if($info->MsgType == "event" && $info->Event == "user_gifting_card") {
//            //转赠事件推送
//            //转增事件获得，应删除相应人员的会员卡记录
//            $Dyh->userGiftCard($info->FromUserName,$info->UserCardCode);
//        }
        else if($info->MsgType == "event" && $info->Event == "user_view_card") {
            // 解决inner error的问题
            $Dyh->userViewCard($info->FromUserName,$info->UserCardCode,$info->CardId);
//            $vipuser = new  \app\home\model\Vipuser();
//            $isa = new  \app\home\model\Isa();
//            $isa->updata($info->FromUserName,array("code"=>$info->UserCardCode));
//            $vipuser->updata($info->FromUserName,array("code"=>$info->UserCardCode));
        }
        else if($info->MsgType == "event" && $info->Event == "subscribe") {
//            $Dyh->userSubscribe($info->FromUserName);
            $wxResponse->backTw($info->FromUserName,$info->ToUserName,"欢迎来到安财人的聚集地！","我们身处各行各业，我们分布世界各地，我们是彼此最坚实的后盾。","http://wx.aufe.vip/aufecmu/index.php?s=Home/Person");
        }
        else if($info->MsgType == "event" && $info->Event == "TEMPLATESENDJOBFINISH") {
            \think\Log::write(json_encode($info),'模板消息数据报：');
        }
        else{
            echo "";
            die;
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


