<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/6/7
 * Time: 下午10:43
 * 这里用于处理公众号后天的管理任务，处理如下任务：
 * 1、获取订阅号标签；
 * 2、设置订阅号标签（新增一个标签）；
 * 3、设置该用户到某个标签；
 * 4、删除一个标签；
 */

namespace wxLib;

class Manager {

    protected $accessToken ;

    public function __construct() {
        $this->accessToken=file_get_contents("http://api.aufe.vip/jssdk/zacToken");
    }

    public function upAccessToken() {
        $this->accessToken=file_get_contents("http://api.aufe.vip/jssdk/upZacToken");
    }


    /*
     * 获取所有标签分组
     * */
    public function getLabels() {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/tags/get?access_token=$this->accessToken";
        return $this->curlHttpRequest($requestUrl,null,true);
    }

    /*
     * 新增一个订阅号标签
     * */
    public function addNewLabels($name) {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/tags/create?access_token=$this->accessToken";
        $createCardPostJson=array(
            "tag"=>array(
                "name"=>$name
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    /*
     * 为批量用户设置标签，一个用户也使用这个借口（不晓得是不是微信那边懒，不给单独的接口）
     * */
    public function setUserLabel($openList,$tagId) {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=$this->accessToken";
        $createCardPostJson=array(
            'openid_list'=>$openList,
            'tagid'=>$tagId
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    /*
     * 删除一个标签
     * */
    public function deleteLabel($id) {
        $requestUrl="https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=$this->accessToken";
        $createCardPostJson=array(
            "tag"=>array(
                "id"=>$id
            )
        );
        $createCardPostJson=json_encode($createCardPostJson,JSON_UNESCAPED_UNICODE  );
        return $this->curlHttpRequest($requestUrl,null,true,$createCardPostJson);
    }

    private function curlHttpRequest($url,$cookie = null,$skipssl = false ,$postDate = "") {
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
//            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);  php7开始删除该选项
            curl_setopt($ch ,CURLOPT_POST ,1);
            curl_setopt($ch ,CURLOPT_POSTFIELDS ,$postDate );
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        curl_close($ch);
        return $tmpInfo;
    }

}
