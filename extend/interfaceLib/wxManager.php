<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/6/10
 * Time: 下午3:26
 */
namespace interfaceLib;
use curlLib\Multi;
use wxLib\Manager;
class wxManager extends Manager {
    private $localLabelPath;

    public function __construct()
    {
        parent::__construct();
        $this->localLabelPath = ENTRA_PATH.'public/static/label.json';
    }

    /*
     * 为一组openid打上tagId的标签
     * */
    public function setLabel($openidList,$tagId) {
        $listArr = [];
        $openList = [];
        $allCount = count($openidList);
        $result = [];
        for ($i = 1 ; $i <= $allCount /50 + 1 ; ++$i,$openList = []) {
            for($j = ($i - 1) *50 ; $j < $i * 50 && $j < $allCount ; ++$j ) {
                $openList[] = $openidList[$j];
            }
            $listArr[]=[
                'url'=>"https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=$this->accessToken",
                'postJson'=>json_encode(array(
                    'openid_list'=>$openList,
                    'tagid'=>$tagId
                ),JSON_UNESCAPED_UNICODE),
            ];
//            $result[$i-1] = json_decode($this->setUserLabel($openList,$tagId),true);
        }
        $multi = new Multi();
        return $multi->cutlMulti($listArr);
//        return $listArr;
    }

    /*
     * 批量删除星标
     * */
    public function deleteLabelList() {
        for($i = 120 ; $i <= 133 ; $i++ ) {
            $this->deleteOneLabel($i,true);
        }
    }

    /*
     * 批量增加星标
     * */
    public function newLabelList() {
        for($index = 2001 ; $index <= 2017 ; $index++ ) {
            $this->newOneLabel($index,$index.'级',true);
        }
    }

    /*
     * 新增一个标签
     * */
    public function newOneLabel($labelOutCode,$labelName,$debug = false) {
        $arr = json_decode($this->addNewLabels($labelName),true);
        if( $debug ) {
            echo "新增标签{$labelName}的执行结果：";
            dump($arr);
        }
        if(empty($arr['errcode'])) {
            return $this->saveLabel($arr['tag']['id'],$labelOutCode,$arr['tag']['name']);
        }
        else if( $arr['errcode'] == '40001') {
            $this->upAccessToken(); // 要求更新accessToken
            $arr = json_decode($this->addNewLabels($labelName),true);
            if(empty($arr['errcode'])) {
                return $this->saveLabel($arr['tag']['id'],$labelOutCode,$arr['tag']['name']);
            }
        }
        return -2;
    }

    /*
     * 将新增的标签写入本地文件中保存下来
     * 传入三个参数，分别是
     * labelId：标签的Id
     * labelOutCode：标签外接码，用于和系统中其他模块对接
     * labelName：标签名称
     * */
    public function saveLabel($labelId,$labelOutCode,$labelName) {
        $labelArr = json_decode(file_get_contents($this->localLabelPath),true);
        foreach ($labelArr as $item) {
            if($item['labelId'] == $labelId) {
                return -1;
            }
        }
        $labelArr[] = [
            'labelId'=>$labelId,
            'labelOutCode'=>$labelOutCode,
            'labelName'=>$labelName,
        ];
        file_put_contents($this->localLabelPath,json_encode($labelArr,JSON_UNESCAPED_UNICODE));
        return 1;
    }

    /*
     * 删除一个星标
     * */
    public function deleteOneLabel($labelId,$debug = false) {
        $arr = json_decode($this->deleteLabel($labelId),true);
        if( $debug ) {
            echo "删除星标{$labelId}执行结果：";
            dump($arr);
        }
        if($arr['errcode'] == '0') {
            return $this->deleteLocalLabel($labelId);
        }
        else {
            return -1;
        }
    }

    /*
     * 删除一个本地星标存储
     * */
    public function deleteLocalLabel($labelId) {
        $labelArr = json_decode(file_get_contents($this->localLabelPath),true);
        foreach ($labelArr as $index => $item) {
            if($item['labelId'] == $labelId) {
                unset($labelArr[$index]);
                file_put_contents($this->localLabelPath,json_encode($labelArr,JSON_UNESCAPED_UNICODE));
                return 1;
            }
        }
        return 2;
    }

    /*
     * 查看本地存储
     * */
    public function showLocalLabelList() {
        dump(json_decode(file_get_contents($this->localLabelPath),true));
    }

    /*
     * 根据外接labelCode获取tagid
     * */
    public function getTagId($labelOutCode) {
        $labelArr = json_decode(file_get_contents($this->localLabelPath),true);
        foreach ($labelArr as $index => $item) {
            if($item['labelOutCode'] == $labelOutCode) {
                return $item['labelId'];
            }
        }
        return 154; // 表示未激活，未能找到该用户的星标信息
    }
}
