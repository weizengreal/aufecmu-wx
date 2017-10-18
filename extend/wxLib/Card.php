<?php
/**
 * Created by PhpStorm.
 * User: WeiZeng
 * Date: 2016/12/12
 * Time: 13:57
 *  我的服务号openid：o16hwwb9HjRJ9uxDHWqd4FoHdeFI
 */
namespace wxLib;

/*
http://mmbiz.qpic.cn/mmbiz_png/dHr5jWLaicWibicHMWicic0mgU3kicMcpia89HIXf83PUyG5ib2bO7pfAJ4BwwwibdGVMicWJvLI9N9iattju6JibQgKsD6tmA/0
http://mmbiz.qpic.cn/mmbiz_png/dHr5jWLaicWibicHMWicic0mgU3kicMcpia89HIpicrVm6j9sR3mKTYU4TO0VDVaWrY4IJaCMch7GlYoWUggTDSpqclzMw/0
http://mmbiz.qpic.cn/mmbiz_jpg/dHr5jWLaicW8ASbTJFKUCI8ibsyXhypQ26KtvsOFYXwIH8Mn2yicmUFFZ25o0P3uicS2pt56WwLibibgxY5lPpCQLTDw/0
http://mmbiz.qpic.cn/mmbiz_jpg/dHr5jWLaicW9kF3IqJoGclq0nTUoDAPhmXFPLwWHRXfnpsjAe2dicqZ6y3GbAwCTjLTibUPsejUPHyEBkFIHX117g/0
 * */

class Card {
    private $accessToken ;

    public function __construct() {
        $this->accessToken=file_get_contents("http://api.aufe.vip/jssdk/zacToken");
    }

    public function upAccessToken() {
        $this->accessToken=file_get_contents("http://api.aufe.vip/jssdk/upZacToken");
    }

    /*
     * 传入图片的本地相对路径，相对于该项目的根目录下的路径
     * */
    public function upImg($img) {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=$this->accessToken";

        $real_path= realpath($img);
//        $real_path=ENTRA_PATH.$img;
        $real_path=new \CURLFile($real_path);
//        return $real_path;
        $createCardPostJson= array("buffer"=>$real_path);
//        return $createCardPostJson;
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    /*
     * 创建会员卡功能，每次创建一张会员卡都
     * 将需要为该会员卡在本地创建一个该cardid的索引
     * */
    public function createVipCard() {
        $requestUrl="https://api.weixin.qq.com/card/create?access_token=$this->accessToken";
        $logoUrl="http://mmbiz.qpic.cn/mmbiz_png/mJicvPn41MsCKia4dpXsj7pRic3aNUSS8yic8bdjYZuDyalM0g4mQRoRCV3ckCVuRp8fadgD1MIIQ9c3kROT4PkqPQ/0";
        $backgroundImg="http://mmbiz.qpic.cn/mmbiz_jpg/dHr5jWLaicW9kF3IqJoGclq0nTUoDAPhmXFPLwWHRXfnpsjAe2dicqZ6y3GbAwCTjLTibUPsejUPHyEBkFIHX117g/0";
        $activate_url="http://wx.ancai4399.com/aufecmu/index.php";
        $base_info=array(
            'logo_url'=>$logoUrl,
            'code_type'=>'CODE_TYPE_QRCODE',
            'brand_name'=>'AUFE',
            'title'=>'安财校友卡',
            'color'=>'Color030',
            'notice'=>'安财校友们的聚集地',
            'description'=>'欢迎来到我们的聚集地，安财校友汇！',
            'sku'=>array(
                'quantity'=>30,
            ),
            'date_info'=>array(
                'type'=>'DATE_TYPE_PERMANENT',
                // 'begin_timestamp'=>time(),
                // 'end_timestamp'=>time()+86400*15,//测试级别的会员卡，三天有效期
            ),
            "can_give_friend" => false,
            'center_title'=>'个人中心',
            'center_sub_title'=>'更多功能请进入个人中心',
            'center_url'=>'http://wx.ancai4399.com/',
        );
        $createCardPostJson=array(
            "card"=>array(
                "card_type"=> "MEMBER_CARD",
                "member_card"=>array(
                    "background_pic_url"=>$backgroundImg,
                    "base_info"=>$base_info,
                    "supply_bonus"=> false,
                    "bonus_url"=>"http://wx.ancai4399.com/",
                    "supply_balance"=> false,
                    "prerogative"=> "本会员卡为安徽财经大学校友卡，欢迎使用！",
                    "auto_activate"=> false,
                    "custom_field1"=>array(
                        "name"=> "余额",
                        "url"=> "http://www.qq.com"
                    ),
                    "custom_field2"=>array(
                        "name"=> "字段测试2",
                        "url"=> "http://www.qq.com"
                    ),
                    "custom_field3"=>array(
                        "name"=> "字段测试3",
                        "url"=> "http://www.qq.com"
                    ),
                    "activate_url"=> $activate_url,
                    "custom_cell1"=>array(
                        "name"=> "使用入口1",
                        "tips"=> "激活后显示",
                        "url"=> "http://wx.ancai4399.com"
                    )
                )
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        // return $createCardPostJson;
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    public function alterVipCardBg($backgroundImg) {
        $requestUrl="https://api.weixin.qq.com/card/update?access_token=$this->accessToken";
        $createCardPostJson=array(
            "card_id"=>"p16hwwfX9hCa3rXu-GbvSitwBTmo",
            "member_card"=>array(
                "background_pic_url"=>$backgroundImg,
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    public function _alterVipCard($card_id) {
        $requestUrl="https://api.weixin.qq.com/card/update?access_token=$this->accessToken";
        $base_info=array(
            'title'=>'校友卡(已过期)',
        );
        $createCardPostJson=array(
            "card_id"=>"$card_id",
            "member_card"=>array(
                "base_info"=>$base_info,
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    //p16hwwfX9hCa3rXu-GbvSitwBTmo  永久的
    public function alterVipCard() {
        $requestUrl="https://api.weixin.qq.com/card/update?access_token=$this->accessToken";
        $centerUrl="http://wx.aufe.vip/aufecmu/index.html";
        $base_info=array(
            'center_url'=>$centerUrl,
        );
        $createCardPostJson=array(
            "card_id"=>"p16hwwfX9hCa3rXu-GbvSitwBTmo",
            "member_card"=>array(
                "base_info"=>$base_info,
                "custom_field1"=>array(
                    "name"=> "年级",
                    "url"=> $centerUrl
                ),
                "custom_field2"=>array(
                    "name"=> "姓名",
                    "url"=> $centerUrl
                ),
                "custom_field3"=>array(
                    "name"=> "已相识",
                    "url"=> $centerUrl
                ),
                "custom_cell1"=>array(
                    "name"=> "领取排名",
                    "tips"=> "",
                    "url"=> getWxAuthUrl("","http://wx.aufe.vip/aufecmu_v4/rank")
                ),
                "custom_cell2"=>array(
                    "name"=> "切换封面",
                    "tips"=> "",
                    "url"=> getWxAuthUrl("base","http://wx.aufe.vip/aufecmu_v4/change")
                ),
                "activate_url"=> getWxAuthUrl("base","http://wx.aufe.vip/aufecmu_v4/active"),
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    public function getIpo() {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token=$this->accessToken";
        $createCardPostJson=array(
            'begin'=>0,
            'limit'=>10
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
//        return $createCardPostJson;
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }


    //
    public function changeCardLogo() {
        $requestUrl="https://api.weixin.qq.com/card/update?access_token=$this->accessToken";
        //
        $logoUrl1="http://mmbiz.qpic.cn/mmbiz_png/mJicvPn41MsCKia4dpXsj7pRic3aNUSS8yic8bdjYZuDyalM0g4mQRoRCV3ckCVuRp8fadgD1MIIQ9c3kROT4PkqPQ/0";
        $logoUrl="http://mmbiz.qpic.cn/mmbiz_png/dHr5jWLaicWibicHMWicic0mgU3kicMcpia89HImialkISLyiaibwwkq8wGwRL7kcdsTibG2ic54cuPRlJYibFSZiaQ3IaU08t7w/0";
        $backgroundImg="http://mmbiz.qpic.cn/mmbiz_png/dHr5jWLaicWibicHMWicic0mgU3kicMcpia89HIpicrVm6j9sR3mKTYU4TO0VDVaWrY4IJaCMch7GlYoWUggTDSpqclzMw/0";
        $base_info=array(
            'logo_url'=>$logoUrl,
        );
        $createCardPostJson=array(
            "card_id"=>"p16hwwfX9hCa3rXu-GbvSitwBTmo",
            "member_card"=>array(
//                "background_pic_url"=>$backgroundImg,
                "base_info"=>$base_info
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    public function changeCardActiviteUrl($cardid="p16hwwfX9hCa3rXu-GbvSitwBTmo") {
        $activate_url = getWxAuthUrl("base",url("index/index/active","",false,true),"" );
        $requestUrl="https://api.weixin.qq.com/card/update?access_token=$this->accessToken";
        $createCardPostJson=array(
            "card_id"=>$cardid,
            "member_card"=>array(
                "activate_url"=> $activate_url,
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    public function getImg($card_id) {
        $postJson=json_encode(array(
            "action_name"=> "QR_CARD",
            'action_info'=>array(
                'card'=>array(
                    'card_id'=>"$card_id"
                )
            )
        ));
        $requestUrl="https://api.weixin.qq.com/card/qrcode/create?access_token=$this->accessToken";
        return $this->curlHttpRequest($requestUrl,null,true,$postJson);
    }

    /*
     * 增加会员卡的库存
     * */
    public function addCardNum($addCount) {
        $postJson=json_encode(array(
            'card_id'=>"p16hwwfX9hCa3rXu-GbvSitwBTmo",
            'increase_stock_value'=>"$addCount"
        ));
        $requestUrl="https://api.weixin.qq.com/card/modifystock?access_token=$this->accessToken";
        return $this->curlHttpRequest($requestUrl,null,true,$postJson);
    }

    /*
     * encrypt_code解码为code，用于激活会员卡
     * 传入值：该用户code加密字符串encrypt_code，该参数需要先经过urldecode
     * 返回值：该用户针对该会员卡的code
     * */
    public function decodeForCode($encrypt_code) {
        $postJson=json_encode(array(
            'encrypt_code'=>"$encrypt_code"
        ));
        // return $postJson;
        $requestUrl="https://api.weixin.qq.com/card/code/decrypt?access_token=$this->accessToken";
        $res = json_decode($this->curlHttpRequest($requestUrl,null,true,$postJson),true);
        if($res['errcode'] == "40001") {
            //accessToken过期，要求接口部分更新accessToken
            $this->upAccessToken();
            $requestUrl="https://api.weixin.qq.com/card/code/decrypt?access_token=$this->accessToken";
            $res = json_decode($this->curlHttpRequest($requestUrl,null,true,$postJson),true);
        }
        return $res['code'];
    }

    /*
     * 根据code码激活该用户
     * 此接口可以为用户自定义卡券
     * */
    public function setUserActive($code,$memberNumber,$money=100) {
        $postJson=json_encode(array(
            "membership_number"=> $memberNumber,
            "code"=> $code,
            "init_custom_field_value1"=> $money."元",
        ),JSON_UNESCAPED_UNICODE);
        $requestUrl="https://api.weixin.qq.com/card/membercard/activate?access_token=$this->accessToken";
        return $this->curlHttpRequest($requestUrl,null,true,$postJson);
    }

    /*
     * 接入教务系统的用户激活
     * 接收参数
     * encrypt_code   加密之后的code
     * studentid  学号
     * */
    public function setUserActiveInJwc($encrypt_code , $studentid) {
        $code=$this->decodeForCode($encrypt_code);
        $res=json_decode($this->setUserActive($code,$studentid),true);
        if($res['errcode'] == "0") {
            return true;
        }
        else {
            return false;
        }
    }

    /*
     * 自定义会员卡相关信息接口
     * */
    public function setUserActiveSy($code,$memberNumber) {
        $postJson=json_encode(array(
            "membership_number"=> $memberNumber,
            "code"=> $code,
            "init_custom_field_value1"=> "2014年",
            "init_custom_field_value2"=> "郑伟",
            "init_custom_field_value3"=> "男",
        ),JSON_UNESCAPED_UNICODE);
        $requestUrl="https://api.weixin.qq.com/card/membercard/activate?access_token=$this->accessToken";
        return $this->curlHttpRequest($requestUrl,null,true,$postJson);
    }

    /*
     * for校友圈version2.0
     * */
    public function setXyqActive($code , $studentid , $grade ,$name ,$day) {
//        $code=$this->decodeForCode($encrypt_code);
        $postJson=json_encode(array(
            "membership_number"=> $studentid,
            "code"=> $code,
            "init_custom_field_value1"=> $grade,
            "init_custom_field_value2"=> $name,
            "init_custom_field_value3"=> $day,
        ),JSON_UNESCAPED_UNICODE);
        $requestUrl="https://api.weixin.qq.com/card/membercard/activate?access_token=$this->accessToken";
        $res = json_decode($this->curlHttpRequest($requestUrl,null,true,$postJson),true) ;
//        file_put_contents("test.txt",json_encode($res).PHP_EOL,FILE_APPEND);
        if($res['errcode'] == "0") {
            return array(
                'code'=>$code,
                'errcode'=>1
            );
        }
        else {
            \think\Log::write(json_encode($res),"WARNING INNER CARD");
            return array(
                'errcode'=>-1
            );
        }
    }

    //1491990491IpK_1T69hDhZkLQTlwsAXwGDn08FaIvtb_0YJjQdDAQ8fOKzzjmMqDXvZhxvdyw-LB9n4xDvq6b8OuzBpNawXQmVgG7ONISXaDPARrp16hwwfX9hCa3rXu-GbvSitwBTmorank
    //1491990491IpK_1T69hDhZkLQTlwsAXwGDn08FaIvtb_0YJjQdDAQ8fOKzzjmMqDXvZhxvdyw-LB9n4xDvq6b8OuzBpNawXQmVgG7ONISXaDPARrp16hwwfX9hCa3rXu-GbvSitwBTmo


    /*
     * for校友圈version2.0
     * */
    public function setXyqActiveForChangeBg($code , $studentid , $grade ,$name ,$day , $bgUrl ) {
//        $code=$this->decodeForCode($encrypt_code);
        $postJson=json_encode(array(
            "membership_number"=> $studentid,
            "code"=> $code,
            "init_custom_field_value1"=> $grade,
            "init_custom_field_value2"=> $name,
            "init_custom_field_value3"=> $day,
            "background_pic_url"=>$bgUrl
        ),JSON_UNESCAPED_UNICODE);
        $requestUrl="https://api.weixin.qq.com/card/membercard/activate?access_token=$this->accessToken";
        $res = json_decode($this->curlHttpRequest($requestUrl,null,true,$postJson),true) ;
//        \think\Log::write($postJson,"Warning msg:");
        if($res['errcode'] == "0") {
            return true;
        }
        else {
//            \think\Log::write(json_encode($res),"Warning msg:");
            return false;
        }
    }




    /*
     * 改变所有人员的会员卡信息
     * */
    public function alterEveryInfo() {
        $requestUrl="https://api.weixin.qq.com/card/update?access_token=$this->accessToken";
//        $logoUrl="http://mmbiz.qpic.cn/mmbiz_jpg/mJicvPn41MsAbUhdgYwHr1p6ic0Mib7zyv8o3DVGBclf2aXticUTaeSUL9KteOLcBrvfBvyyR7qp9gKic3XwyO5WgVw/0";
//        $backgroundImg="http://mmbiz.qpic.cn/mmbiz_png/mJicvPn41MsCKia4dpXsj7pRic3aNUSS8yicl6XkxQpUw6fWc1iaAms4fKE7Kcrx6dzlgy21OWPozEEGFstDPlicAOnw/0";
//        $baseUrl="http://wx.aufe.vip/aufecmu/index.php?s=Home/";
        $base_info=array(
//            'description'=>'我们身处各行各业，我们分布世界各地。我们是彼此最坚实的后盾',
            'center_title'=>'校友签到',
        );
        $createCardPostJson=array(
            "card_id"=>"p16hwwfX9hCa3rXu-GbvSitwBTmo",
            "member_card"=>array(
//                "activate_url"=> "http://wx.aufe.vip/aufecmu/index.php",
//                "base_info"=>$base_info,
//                "prerogative"=> "这是一张只有安财人才可以领取的卡片",
//                "custom_field1"=>array(
//                    "name"=> "当前余额",
//                ),
                "custom_field1"=>array(
                    "name"=> "年级",
                    "url"=> null
                ),
                "custom_field2"=>array(
                    "name"=> "姓名",
                    "url"=> null
                ),
                "custom_field3"=>array(
                    "name"=> "已入学",
                    "url"=> null
                ),
                "custom_cell1"=>array(
                    "name"=> "我的领取排名",
                    "tips"=> "第100000000名",
//                  "url"=> sprintf(C("baseUrl") ,urlencode(U("Home/Safe/safeRoute","",false,true)),"" ),
                )
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    /*
     * 用于为某一位用户更新会员卡余额
     * */
//    public function updateCampusMoney($code,$money) {
//        $postJson=json_encode(array(
//            "code"=> $code,
//            "init_custom_field_value1"=> $money."元",
//        ),JSON_UNESCAPED_UNICODE);
//        $requestUrl="https://api.weixin.qq.com/card/membercard/activate?access_token=$this->accessToken";
//        return $this->curlHttpRequest($requestUrl,null,true,$postJson);
//    }

    /*
     * 调用获取用户基本信息接口，后台更新用户的头像
     * */
    public function getWxUserInfo($openid) {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->accessToken}&openid=$openid&lang=zh_CN";
        $res = $this->curlHttpRequest($requestUrl,null,true);
        //accessToken过期，过期
        if( !empty($res['errcode']) && $res['errcode'] == "40001") {
            $this->upAccessToken();
            $requestUrl="https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->accessToken}&openid=$openid&lang=zh_CN";
            $res = $this->curlHttpRequest($requestUrl,null,true);
        }
        return $res;
    }

    /*
     * 设置用户白名单
     * */
    public function setTestWhite($openid) {
        $postJson=json_encode(array(
            'openid'=>array(
                '0'=>$openid
            )
        ));
        $requestUrl="https://api.weixin.qq.com/card/testwhitelist/set?access_token=$this->accessToken";
        return $this->curlHttpRequest($requestUrl,null,true,$postJson);
    }

    /*
     * message
     * */
    public function sendMessage($openid,$first,$personName,$date,$detailCot) {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$this->accessToken}";
        $createCardPostJson = '{
           "touser":"'.$openid.'",
           "template_id":"NI06ig0Bsp_COYNKxZViL4QDa05L-_SF_6CkeGH_Jok",
           "url":"http://baidu.com",  
           "data":{
                   "first": {
                        "value":"'.$first.'"
                   },
                   "keyword1":{
                        "value":"'.$personName.'"
                   },
                   "keyword2": {
                        "value":"'.$date.'"
                   },
                   "remark":{
                        "value":"'.$detailCot.'"
                   }
           }
       }';
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    /*
     * 客服消息
     * */
    public function sendKaquan($openid,$cardid="p16hwwfX9hCa3rXu-GbvSitwBTmo") {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$this->accessToken}";
        $createCardPostJson='{
          "touser":"'.$openid.'", 
          "msgtype":"wxcard",
          "wxcard":{              
           "card_id":"'.$cardid.'"        
                    },
        }';
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    /*
     * 获得模板消息列表
     * */
    public function getMessage() {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$this->accessToken}";
        return $this->curlHttpRequest($requestUrl,null,true);
    }

    public function curlHttpRequest($url,$cookie = null,$skipssl = false ,$postDate = "") {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        if(! empty($cookie)) {
            curl_setopt($ch , CURLOPT_COOKIE , $cookie);
        }
        if( $skipssl) {
            curl_setopt($ch , CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch , CURLOPT_SSL_VERIFYHOST , 0);
        }
        if( ! empty($postDate)) {
//            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            curl_setopt($ch ,CURLOPT_POST ,1);
            curl_setopt($ch ,CURLOPT_POSTFIELDS ,$postDate );
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        curl_close($ch);
        return $tmpInfo;
    }



//====================================我是分割线，下面的是测试之后废弃的代码==========================================================================================

//    public function setXyqActive($encrypt_code , $studentid , $grade ,$name ,$day) {
//        $code=$this->decodeForCode($encrypt_code);
//        $postJson=json_encode(array(
//            "membership_number"=> $studentid,
//            "code"=> $code,
//            "init_custom_field_value1"=> $grade,
//            "init_custom_field_value2"=> $name,
//            "init_custom_field_value3"=> $day,
//        ),JSON_UNESCAPED_UNICODE);
//        $requestUrl="https://api.weixin.qq.com/card/membercard/activate?access_token=$this->accessToken";
//        $res = json_decode($this->curlHttpRequest($requestUrl,null,true,$postJson),true) ;
//        file_put_contents("test.txt",json_encode($res).PHP_EOL,FILE_APPEND);
//        if($res['errcode'] == "0") {
//            return array(
//                'code'=>$code,
//                'errcode'=>1
//            );
//        }
//        else {
//            return array(
//                'errcode'=>-1
//            );
//        }
//    }


}








