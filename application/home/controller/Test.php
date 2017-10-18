<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/6/10
 * Time: 上午10:12
 */
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Request;
use interfaceLib\wxManager;

class Test extends Controller {
    private $manager;
    private $localLabelPath;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->manager = new wxManager();
        $this->localLabelPath = ENTRA_PATH.'public/static/label.json';
    }

    public function index() {
//        $this->manager->showLocalLabelList();
//        $dyh = new \app\home\logic\Dyh();
//        dump(json_decode($dyh->getUserInfo('o16hwwRX1OUbDmfy-O5GuoAOvTrc') , true));


        die();

//        return $this->fetch("index/init");


        $res = Db::table('club_vipuser')->field('openid,studentid,name')->where("studentid is null || studentid = ''")->select();
        $openidList = [];
        foreach ($res as $item) {
            $openidList [] = $item['openid'];
        }
        dump($this->manager->setLabel($openidList,154));
//        dump(substr($res[0]['studentid'],0,4));
//        for($i=2010,$openidList = [];$i<2015;++$i,$openidList = []) {
//            $derGrade = $i;
//            echo '当前执行划分的星标组别为：'.$derGrade;
//            foreach ($res as $item) {
//                $grade = substr($item['studentid'],0,4);
//                if(!empty($item['studentid']) && $grade == $derGrade) {
//                    $openidList [] = $item['openid'];
//                }
//            }
//            $tagId = $this->manager->getTagId($derGrade);
//            dump($this->manager->setLabel($openidList,$tagId));
//        }
//        dump($this->deleteOneLabel(134,true));
//        dump($this->saveLabel(2,2,'星标组'));
//        die();
//        dump(json_decode($this->manager->getLabels(),true));
//        dump($this->saveLabel(119,2000,'2000级和以前'));
//        $this->deleteLabelList();
//        $this->newLabelList();
//        dump(json_decode($this->manager->getLabels(),true));
//        $this->newOneLabel('other','未激活校友卡',true);
    }

//    private function deleteLabelList() {
//        for($i = 120 ; $i <= 133 ; $i++ ) {
//            $this->deleteOneLabel($i,true);
//        }
//    }
//
//    /*
//     * 批量增加星标
//     * */
//    private function newLabelList() {
//        for($index = 2001 ; $index <= 2017 ; $index++ ) {
//            $this->newOneLabel($index,$index.'级',true);
//        }
//    }
//
//    /*
//     * 新增一个标签
//     * */
//    private function newOneLabel($labelOutCode,$labelName,$debug = false) {
//        $arr = json_decode($this->manager->addNewLabels($labelName),true);
//        if( $debug ) {
//            echo "新增标签{$labelName}的执行结果：";
//            dump($arr);
//        }
//        if(empty($arr['errcode'])) {
//            return $this->saveLabel($arr['tag']['id'],$labelOutCode,$arr['tag']['name']);
//        }
//        else if( $arr['errcode'] == '40001') {
//            $this->manager->upAccessToken(); // 要求更新accessToken
//            $arr = json_decode($this->manager->addNewLabels($labelName),true);
//            if(empty($arr['errcode'])) {
//                return $this->saveLabel($arr['tag']['id'],$labelOutCode,$arr['tag']['name']);
//            }
//        }
//        return -2;
//    }
//
//    /*
//     * 将新增的标签写入本地文件中保存下来
//     * 传入三个参数，分别是
//     * labelId：标签的Id
//     * labelOutCode：标签外接码，用于和系统中其他模块对接
//     * labelName：标签名称
//     * */
//    private function saveLabel($labelId,$labelOutCode,$labelName) {
//        $labelArr = json_decode(file_get_contents($this->localLabelPath),true);
//        foreach ($labelArr as $item) {
//            if($item['labelId'] == $labelId) {
//                return -1;
//            }
//        }
//        $labelArr[] = [
//            'labelId'=>$labelId,
//            'labelOutCode'=>$labelOutCode,
//            'labelName'=>$labelName,
//        ];
//        file_put_contents($this->localLabelPath,json_encode($labelArr,JSON_UNESCAPED_UNICODE));
//        return 1;
//    }
//
//    /*
//     * 删除一个星标
//     * */
//    private function deleteOneLabel($labelId,$debug = false) {
//        $arr = json_decode($this->manager->deleteOneLabel($labelId),true);
//        if( $debug ) {
//            echo "删除星标{$labelId}执行结果：";
//            dump($arr);
//        }
//        if($arr['errcode'] == '0') {
//            return $this->deleteLabel($labelId);
//        }
//        else {
//            return -1;
//        }
//    }
//
//    /*
//     * 删除一个本地星标存储
//     * */
//    private function deleteLabel($labelId) {
//        $labelArr = json_decode(file_get_contents($this->localLabelPath),true);
//        foreach ($labelArr as $index => $item) {
//            if($item['labelId'] == $labelId) {
//                unset($labelArr[$index]);
//                file_put_contents($this->localLabelPath,json_encode($labelArr,JSON_UNESCAPED_UNICODE));
//                return 1;
//            }
//        }
//        return 2;
//    }
//
//    /*
//     * 查看本地存储
//     * */
//    private function showLocalLabelList() {
//        dump(json_decode(file_get_contents($this->localLabelPath),true));
//    }
//
//    /*
//     * 根据外接labelCode获取tagid
//     * */
//    private function getTagId($labelOutCode) {
//        $labelArr = json_decode(file_get_contents($this->localLabelPath),true);
//        foreach ($labelArr as $index => $item) {
//            if($item['labelOutCode'] == $labelOutCode) {
//                return $item['labelId'];
//            }
//        }
//        return $labelArr[''];
//    }



}
