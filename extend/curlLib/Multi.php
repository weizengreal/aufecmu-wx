<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/5/17
 * Time: 下午12:55
 * curlmulti 实现并发执行
 * 开放handleMultiRes函数用于子类以多态形式处理curl并发执行结果，
 * 默认情况下将返回一个数组，数组将返回curl执行的结果；
 */
namespace curlLib;
class Multi {

    public function cutlMulti($listArr) {
        $handle = $content =array();
        $mh = curl_multi_init();
        foreach ($listArr as $key => $item) {
            $this->curlHttpRequest($handle[$key],$item['url'],null,true,$item['postJson']);
            curl_multi_add_handle($mh , $handle[$key]);
        }
        $active = null;
        do{
            $tmpRes = curl_multi_exec($mh , $active);
        }while($tmpRes == CURLM_CALL_MULTI_PERFORM);
        while ($active && $tmpRes == CURLM_OK) {
            if(curl_multi_select($mh) === -1){
                usleep(100);
            }
            do {
                $tmpRes = curl_multi_exec($mh, $active);
            } while ($tmpRes == CURLM_CALL_MULTI_PERFORM);
        }
        $result = [];
        foreach($handle as $key => $ch) {
            $cot = curl_multi_getcontent($ch);
            $result[$key] = $this->handleMultiRes($cot);
            curl_multi_remove_handle($mh ,$ch);
        }
        curl_multi_close($mh);
        return $result;
    }

    /*
     * 判断是否为json，如果是则自动将json解码为数组格式
     * */
    protected function handleMultiRes($content) {
        $jsonArr = json_decode($content,true);
        if( empty($jsonArr) ) {
            return [
                'curlResult'=>$content
            ];
        }
        else {
            return [
                'curlResult'=>$jsonArr
            ];
        }
    }

    private function curlHttpRequest(&$ch,$url,$cookie = null,$skipssl = false ,$postDate = "") {
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
//            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);  php7 废除
            curl_setopt($ch ,CURLOPT_POST ,1);
            curl_setopt($ch ,CURLOPT_POSTFIELDS ,$postDate );
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    }

}