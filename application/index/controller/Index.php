<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;

class Index extends Controller {

    protected $rule = [
        'name'  =>  'require|max:25|token',
    ];

    public function index() {
        echo getWxAuthUrl("",url("index/Xyq/rank","",false,true),"1" );
//        die("test");
//        $getData = Request::instance()->get();
//        if( isset($getData['openid']) && isset($getData['card_id']) && isset($getData['encrypt_code']) ) {
//            setcookie("encrypt_code",$getData['encrypt_code'],time()+270);
//            setcookie("page_openid",$getData['openid'],time()+270);//该openid目前阶段不可信
//            $this->wxBaseAuth();//微信认证
//        }
//        else {
//            //不是正确的激活链接，导向错误界面
//            $this->redirect(url("index/message/msg_error",array()));
//        }
    }

    /*
     * 激活阶段，当前页面将由用户确认后回调而来，需要进行认证相关信息的比对
     * 认证后需要完成如下步骤：
     * 1、更新isa表
     * 2、
     * */
    public function active() {
        $wxScope = new \wxLib\Scope();
        $scopeData = $wxScope->getToken(Request::instance()->get("code"));
        if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
            session("openid",$scopeData['openid']);
//            setcookie("page_openid",null,time()-1);
            $init = new \app\index\logic\Auth();
            if($init->updateUserInfo($scopeData) ) {
                return  $this->fetch("active",[
                    'detail'=>url("index/index/active_detail"),
                    'auth'=>url("index/index/auth"),
                    "share"=>url("Index/index/share","",false),
                ]);
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"内部错误，请联系管理员，错误位置：激活内部错误阶段一")));
            }
        }
        else {
            $this->redirect(url("index/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>getWxAuthUrl(null,url("index/index/active","",false,true),"" ) )));
        }
    }

    public function active_detail($xm,$sfz) {
        if(!isset($xm) || !isset($sfz)) {
            $this->redirect(url("index/message/msg_error",array('content'=>"您好，请重新打开会员卡界面开始激活！")));
        }
        if(session("?openid")) {
            return  $this->fetch("active_detail",[
                'auth'=>url("index/index/auth_detail"),
                'xm'=>$xm,
                'sfz'=>$sfz,
                "share"=>url("Index/index/share","",false),
            ]);
        }
        elseif (input("?get.code")) {
            $wxScope = new \wxLib\Scope();
            $scopeData = $wxScope->getToken(Request::instance()->get("code"));
            if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
                session("openid",$scopeData['openid']);
                setcookie("page_openid",null,time()-1);
                $init = new \app\index\logic\Auth();
                if($init->updateUserInfo($scopeData) ) {
                    return  $this->fetch("active_detail",[
                        'auth'=>url("index/index/auth_detail"),
                        'xm'=>$xm,
                        'sfz'=>$sfz,
                        "share"=>url("Index/index/share","",false),
                    ]);
                }
                else {
                    $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"内部错误，请联系管理员，错误位置：激活内部错误阶段一")));
                }
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'])));
            }
        }
        else {
            $this->redirect(url("index/message/msg_error",array('title' => '页面超时' , 'content'=>'点击确定重新激活~' , 'buttonCot'=>'重新激活', 'redirectUri'=>getWxAuthUrl(null,url("index/index/active_detail","",false,true),"" ) )));
        }
    }

    /*
     * 用于鉴定用户的信息是否正确的ajax函数接口
     * */
    public function auth() {
        $postData = Request::instance()->post();
        $ru = $this->rule+[
                'sfz' => 'require|min:15|max:18',
                'xm'=>'require',
            ];
        $res=$this->validate($postData,$ru);
        if($res == true) {
            $auth = new  \app\index\logic\Auth();
            return $auth->active($postData);
        }
        else {
            return ['status'=>-1,'info'=>$res];
        }
    }

    /*
     * 用于鉴定用户的信息是否正确的ajax函数接口
     * */
    public function auth_detail() {
        $postData = Request::instance()->post();
        $ru = $this->rule+[
                'sfz' => 'require|min:15|max:18',
                'xm'=>'require',
                'college'=>'require',
                'studentid'=>'require|min:8|max:12',
                'sex'=>'require',
            ];
        $res=$this->validate($postData,$ru);
        if($res == true) {
            $auth = new  \app\index\logic\Auth();
            return $auth->active_detail($postData);
        }
        else {
            return ['status'=>-1,'info'=>$res];
        }
    }



    /*
     * 激活阶段，当前页面将由用户确认后回调而来，需要进行认证相关信息的比对
     * 认证后需要完成如下步骤：
     * 1、更新isa表
     * 2、
     * */
    public function _active() {
        $wxScope = new \wxLib\Scope();
        $scopeData = $wxScope->getToken(Request::instance()->get("code"));
        if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
            session("openid",$scopeData['openid']);
//            setcookie("page_openid",null,time()-1);
            $init = new \app\index\logic\Auth();
            if($init->updateUserInfo($scopeData) ) {
                return  $this->fetch("active",[
                    'detail'=>url("index/index/active_detail"),
                    'auth'=>url("index/index/_auth"),
                    "share"=>url("Index/index/share","",false),
                ]);
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"内部错误，请联系管理员，错误位置：激活内部错误阶段一")));
            }
        }
        else {
            $this->redirect(url("index/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>getWxAuthUrl(null,url("index/index/active","",false,true),"" ) )));
        }
    }

    public function _active_detail($xm,$sfz) {
        if(!isset($xm) || !isset($sfz)) {
            $this->redirect(url("index/message/msg_error",array('content'=>"您好，请重新打开会员卡界面开始激活！")));
        }
        if(session("?openid")) {
            return  $this->fetch("active_detail",[
                'auth'=>url("index/index/_auth_detail"),
                'xm'=>$xm,
                'sfz'=>$sfz,
                "share"=>url("Index/index/share","",false),
            ]);
        }
        elseif (input("?get.code")) {
            $wxScope = new \wxLib\Scope();
            $scopeData = $wxScope->getToken(Request::instance()->get("code"));
            if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
                session("openid",$scopeData['openid']);
                setcookie("page_openid",null,time()-1);
                $init = new \app\index\logic\Auth();
                if($init->updateUserInfo($scopeData) ) {
                    return  $this->fetch("active_detail",[
                        'auth'=>url("index/index/_auth_detail"),
                        'xm'=>$xm,
                        'sfz'=>$sfz,
                        "share"=>url("Index/index/share","",false),
                    ]);
                }
                else {
                    $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"内部错误，请联系管理员，错误位置：激活内部错误阶段一")));
                }
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'])));
            }
        }
        else {
            $this->redirect(url("index/message/msg_error",array('title' => '页面超时' , 'content'=>'点击确定重新激活~' , 'buttonCot'=>'重新激活', 'redirectUri'=>getWxAuthUrl(null,url("index/index/active_detail","",false,true),"" ) )));
        }
    }

    /*
     * 用于鉴定用户的信息是否正确的ajax函数接口
     * */
    public function _auth() {
        $postData = Request::instance()->post();
        $ru = $this->rule+[
                'sfz' => 'require|min:15|max:18',
                'xm'=>'require',
            ];
        $res=$this->validate($postData,$ru);
        if($res == true) {
            $auth = new  \app\index\logic\Auth();
            return $auth->active($postData);
        }
        else {
            return ['status'=>-1,'info'=>$res];
        }
    }

    /*
     * 用于鉴定用户的信息是否正确的ajax函数接口
     * */
    public function _auth_detail() {
        $postData = Request::instance()->post();
        $ru = $this->rule+[
                'sfz' => 'require|min:15|max:18',
                'xm'=>'require',
                'college'=>'require',
                'studentid'=>'require|min:8|max:12',
                'sex'=>'require',
            ];
        $res=$this->validate($postData,$ru);
        if($res == true) {
            $auth = new  \app\index\logic\Auth();
            return $auth->active_detail($postData);
        }
        else {
            return ['status'=>-1,'info'=>$res];
        }
    }








    public function share() {
        $url=urldecode(input("post.url"));
        $type = input("post.type",false);
        $jssdk = new \wxLib\Jssdk();
        if($type) {
            echo json_encode($jssdk->getSignPackageForCard($url));
        }
        else {
            echo json_encode($jssdk->getSignPackage($url));
        }
    }

    //逻辑上应该是只需要改变$index和$author即可实现上传并导入配置文件
    public function addCardBgImg() {
        $index = "7";
        $img="public/resource/xyqbg/bg$index.png";//管理员手动输入
        $author="设计师 2016级 徐亦潇";
        // 先上传到微信cdn
        $card = new \wxLib\Card();
        $res = json_decode($card->upImg($img),true);
        if(!empty($res['url'])) {
            //将图片加到全局封面的配置文件中
            $bg = $res['url'];
            $urlArr = json_decode(file_get_contents("./public/static/bg.json"),"true");
//            $nextIndex = count($urlArr)+1;
            foreach($urlArr as $item ) {
                if($item['index'] == $index) {
                    echo "已经成功添加图片到微信cdn，但是该index已经存在于配置文件中所以未能成功添加，请重新手动添加，作者：".$author."  index：".$index."  <br>图片链接：".$bg;
                    die;
                }
            }
            $urlArr[]=array(
                "img"=>"/".$img,
                "author"=>$author,
                "index"=>$index,
                "bg"=>$bg,
            );
            file_put_contents("./public/static/bg.json",json_encode($urlArr,JSON_UNESCAPED_UNICODE));
            echo "已经成功添加到切换卡面中，作者：".$author."  <br>图片链接：".$bg;
        }
        else {
            echo "图片没有上传成功！详情请参考下方报错信息：<br>";
            dump($res);
        }
    }

    //根据下标删除某一个图片
    public function deleteImg() {
        $index="";
        $urlArr = json_decode(file_get_contents("./public/static/bg.json"),"true");
        $newArr = array();
        foreach($urlArr as $item ) {
            if($item['index'] != $index) {
                //加上
                $newArr[] = $item;
            }
        }
        file_put_contents("./public/static/bg.json",json_encode($newArr,JSON_UNESCAPED_UNICODE));
    }

    public function card1() {
//        echo getWxAuthUrl("base",url("index/xyq/cardget","",false,true),"" );
//        die();


        $card = new \wxLib\Card();
        dump(json_decode($card->sendKaquan("o16hwwb9HjRJ9uxDHWqd4FoHdeFI"),true));
//        dump($card->_sendMessage("o16hwwb9HjRJ9uxDHWqd4FoHdeFI"));
//        dump($card->sendMessage("o16hwwb9HjRJ9uxDHWqd4FoHdeFI","first","liuyanren",date("Y-m-d G:i"),"remark!!!"));
    }

    public function card2() {
        $img="public/cache/bg6.png";
        $card = new \wxLib\Card();
//        dump($card->getIpo());
        $bg="http://mmbiz.qpic.cn/mmbiz_png/dHr5jWLaicWibicj5behCQcB5o31UDyCBX0iamYQ4r7EST5bogAhibhgLiaYD4NrZMcpxrdm0eL8vP0UMJCHgZ0KlpBQ/0";
//        $bg="http://mmbiz.qpic.cn/mmbiz_png/dHr5jWLaicWicmegRB2iabM7Sk89mcHPcib6uIh1O3MWNAe86tS2zAUgwE2R8gJjricjicKnSosItoN4ymBGHcKD5Rnw/0";
//        dump($card->alterVipCard());die;
        dump($bg);
        dump(json_decode($card->alterVipCardBg($bg),true));
    }

    public function test() {

        echo getDays(2012);
        echo "<br>";
        echo getDays(2011);
        die;
        $urlArr = json_decode(file_get_contents("./public/static/bg.json"),"true");
        dump($urlArr);

        die();
        $code="4L0W6jWl n/r36b8pTRD iezuIILLClLEAiHKud5yqw=";
        echo password_hash(str_replace(" ","+",$code), PASSWORD_DEFAULT);
//        $init = new \app\index\logic\Init();
//        echo $init->test();
    }

    /*
     * 激活前的微信认证
     * */
    private function wxAuth() {
        $this->redirect(getWxAuthUrl(null,url("index/index/active","",false,true),"" ));
    }

    /*
     * 激活前的微信认证
     * */
    private function wxBaseAuth() {
        $this->redirect(getWxAuthUrl("base",url("index/index/active","",false,true),"" ));
    }

    /*
     * 销毁cookie
     * */
    private function destroy() {
        setcookie("page_openid",null,time()-1);
        setcookie("encrypt_code",null,time()-1);
    }

}
