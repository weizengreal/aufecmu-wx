<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/30
 * Time: 下午2:23
 */

namespace app\index\model;
use think\Model;

class Isa extends Model{

    /*
     * 允许传入数组和非数组以验证是否存在，存在返回 true
     * */
    public function isExist($openidArr) {
        if(!is_array($openidArr)) {
            $openidArr=array(
                'openid'=>$openidArr
            );
        }
        if($this->where($openidArr)->count() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    //新增数据
    public function addNew($arr) {
        if($this->insert($arr)) {
            return true;
        }
        else{
            return false;
        }
    }

    //批量传入关联数组以获取基本信息，返回一条数据
    public function getInfoByArr($arr) {
        return $this->where($arr)->find();
    }

    /*
     * 根据openid更新函数
     * 参数1：需要更改的用户openid
     * 参数2：待更新的关联数组
     * */
    public function updata($openid,$upArr) {
        if($this->where("`openid`='$openid'")->update($upArr) !== false){
            return true;
        }
        else{
            return false;
        }
    }

    public function getInfoForChangeBg($openid) {
        return $this->field("club_isa.studentid,club_isa.openid,club_isa.code,club_xs.name")->join("__XS__ "," club_xs.studentid=club_isa.studentid and club_isa.openid='$openid' ")->find();
    }

    /*
     * 一个专用于读取两表用户个人信息
     * */
    public function getInfo($openid,$status=1) {
        return $this->join("__STUDENT__ on __ISA__.studentid = __STUDENT__.studentid and __ISA__.openid='$openid' and __ISA__.status='$status'")->find();
    }

    /*
     * 获取用户领卡排名
     * */
    public function getRank($openid) {
        $res = $this->query("SELECT count(*) as cou FROM `club_isa` WHERE addtime < (SELECT `addtime` from club_isa where `openid`='$openid')");
//        file_put_contents("test.txt",json_encode($res).PHP_EOL,FILE_APPEND);
//        return 8;
        return $res[0]['cou']+1;
    }

    /*
     * 获取排名
     * 逻辑上我们按照vipuser表上的时间进行排名，
     * 但是isa表才有学号数据，xs表才有姓名
     * */
    public function getPageRank($start,$limit=20) {
        $whereStr = "`club_isa`.`openid` != 'o16hwwb9HjRJ9uxDHWqd4FoHdeFI' ";
        $arr = array(
            'o16hwwfcPRKulZ2YkuMia-PJUT9w',
            'o16hwwWRRGzrw8Z6mve8h-bPxUMY',
            'o16hwwf3Rn0eIYnDwsIpzI-IkZOc',
            'o16hwwcltUVihs1iX97epRtg_Pwg',
            'o16hwwWItO4oz_9hihVfPfKhUzFw',
            'o16hwwRX1OUbDmfy-O5GuoAOvTrc',
            'o16hwwRpKYeaZdm3SnKX7zWvARjA',
            'o16hwwVIL2QXSD8zIy2pSfDuvniM',
        );
        foreach ($arr as $item) {
            $whereStr.="and `club_isa`.`openid` != '$item' ";
        }
//        $whereStr = "1";
        $res = $this->field("club_isa.headimgurl as headImgUrl,club_xs.grade,club_xs.name")
            ->where($whereStr)
            ->join("__XS__","club_isa.studentid=club_xs.studentid")
            ->order("club_isa.addtime asc")
            ->limit($start,$limit)
            ->select();
        return $res;
    }

    //"`club_isa.openid`='$openid'"
    public function getOneRank($openid) {
        $res = $this->field("club_isa.headimgurl as headImgUrl,club_xs.grade,club_xs.name")->join("__XS__","club_isa.studentid=club_xs.studentid and club_isa.openid='$openid'")->find();
        return $res;
    }


    //--------------------------------------分隔线，以下函数用于测试使用---------------------------------------------------------------------------------------
    //根据学号获取用户信息
    public function getInfoByStu($studentid) {
        $selectArr=array('studentid'=>$studentid);
        $res=$this->where($selectArr)->limit(1)->select();
        if(!empty($res)){
            return $res[0];
        }
        else{
            return null;
        }
    }

    //根据index获取用户信息
    public function getInfoByIndex($index){
        $res=$this->where(1)->limit($index,1)->select();
        if(!empty($res)){
            return $res[0];
        }
        else{
            return null;
        }
    }

    //新增数据，
    public function addNewForAc($arrAc){
        if(! $this->isExist($arrAc['unionid'],$arrAc['studentid'])){
            if($this->data($arrAc)->add()){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
    }
}