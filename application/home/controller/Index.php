<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Url;
use app\home\logic\Login;
use app\home\logic\Auth;


class Index extends Controller {

    protected $rule = [
        'name'  =>  'require|max:25|token',
    ];


    /*
     * h5全局权限函数
     * 内部逻辑：
     * 判断数据库当前用户的权限是否过期，
     * 过期：更新时间+accessToken
     * 未过期：更新时间并使用当前accessToken
     *
     * 接收一个get参数redirectUri用于回调
     * 接收一个get参数state由于确定回调位置默认为1，回调到spa应用中，需要组合URL
     * */
    public function index() {
        $wxScope = new \wxLib\Scope();
        $code = Request::instance()->get("code");
        $login = new Login();
        $scopeData = $wxScope->getUnionid($code);
        if(empty($scopeData['unionid'])) {
            $this->redirect(url("home/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>urlencode('https://www.baidu.com') )));
        }
        $scopeData['code'] = $code;
        $res = $login->login($scopeData,true);
        if($res['status']) {
//            dump($_GET['redirectUri']);
            return $this->fetch("index",[
                'access_token'=>$res['access_token'],
                'timeOut'=>$res['timeOut'],
                'headimgurl'=>$res['headimgurl'],
                'nickname'=>$res['nickname'],
                'url'=>$_GET['redirectUri']
            ]);
        }
        else {
            $this->redirect(url("home/message/msg_error",array('title' => '内部错误，请重试' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>'http://www.baidu.com' )));
        }
    }

    public function share() {
        $hostName = 'http://wx.aufe.vip/aufecmu';
        if(Request::instance()->get('product') == '1') {
            $this->redirect($hostName.'/index.html/#'.Request::instance()->get('fullpath'));
        }
        else {
            // 生产环境的阿尔法测试版本
            $this->redirect($hostName.'dev/index.html/#'.Request::instance()->get('fullpath'));
        }
    }


    public function init () {
        return $this->fetch("init");
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
            return  $this->fetch("active",[
                'detail'=>url("home/index/active_detail"),
                'auth'=>url("home/index/auth"),
                "share"=>url("home/index/share","",false),
            ]);
        }
        else {
            $this->redirect(url("home/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'] , 'redirectUri'=>getWxAuthUrl(null,url("home/index/active","",false,true),"" ) )));
        }
    }

    public function active_detail($xm,$sfz) {
        if(!isset($xm) || !isset($sfz)) {
            $this->redirect(url("home/message/msg_error",array('content'=>"您好，请重新打开会员卡界面开始激活！")));
        }
        if(session("?openid")) {
            return  $this->fetch("active_detail",[
                'auth'=>url("home/index/auth_detail"),
                'xm'=>$xm,
                'sfz'=>$sfz,
                "share"=>url("home/index/share","",false),
            ]);
        }
        elseif (input("?get.code")) {
            $wxScope = new \wxLib\Scope();
            $scopeData = $wxScope->getToken(Request::instance()->get("code"));
            if( !empty($scopeData['openid']) && !empty($scopeData['access_token']) ) {
                session("openid",$scopeData['openid']);
                setcookie("page_openid",null,time()-1);
                return  $this->fetch("active_detail",[
                    'auth'=>url("home/index/auth_detail"),
                    'xm'=>$xm,
                    'sfz'=>$sfz,
                    "share"=>url("home/index/share","",false),
                ]);
//                $init = new Auth();
//                if($init->updateUserInfo($scopeData) ) {
//                    return  $this->fetch("active_detail",[
//                        'auth'=>url("home/index/auth_detail"),
//                        'xm'=>$xm,
//                        'sfz'=>$sfz,
//                        "share"=>url("home/index/share","",false),
//                    ]);
//                }
//                else {
//                    $this->redirect(url("home/message/msg_error",array('title' => '内部错误' , 'content'=>"内部错误，请联系管理员，错误位置：激活内部错误阶段一")));
//                }
            }
            else {
                $this->redirect(url("home/message/msg_error",array('title' => '微信授权错误' , 'content'=>$scopeData['viewMsg'])));
            }
        }
        else {
            $this->redirect(url("home/message/msg_error",array('title' => '页面超时' , 'content'=>'点击确定重新激活~' , 'buttonCot'=>'重新激活', 'redirectUri'=>getWxAuthUrl(null,url("home/index/active_detail","",false,true),"" ) )));
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
            $auth = new  Auth();
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
            $auth = new  Auth();
            return $auth->active_detail($postData);
        }
        else {
            return ['status'=>-1,'info'=>$res];
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
        // 1、更换会员卡界面所有连接
        $card = new \wxLib\Card();
        dump(json_decode($card->alterVipCard()));
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
        return $this->fetch("init");
        echo getWxAuthUrl("base","http://wx.aufe.vip/aufecmu_v4/active")."<br>";
        echo getWxAuthUrl("base","http://wx.aufe.vip/aufecmu_v4/change")."<br>";

        echo getWxAuthUrl("","http://wx.aufe.vip/aufecmu_v4/rank")."<br>";
        echo getWxAuthUrl("","http://wx.aufe.vip/aufecmu_v4/xyq")."<br>";
    }

    /*
     * 激活前的微信认证
     * */
    private function wxAuth() {
        $this->redirect(getWxAuthUrl(null,url("home/index/active","",false,true),"" ));
    }

    /*
     * 激活前的微信认证
     * */
    private function wxBaseAuth() {
        $this->redirect(getWxAuthUrl("base",url("home/index/active","",false,true),"" ));
    }

    /*
     * 销毁cookie
     * */
    private function destroy() {
        setcookie("page_openid",null,time()-1);
        setcookie("encrypt_code",null,time()-1);
    }

}
