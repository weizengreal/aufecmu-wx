<?php
/**
 * Created by PhpStorm.
 * User: ancai4399
 * Date: 2016/12/18
 * Time: 12:50
 */

namespace app\home\model;
use think\Model;

//openid对应的是用户针对会员卡所属订阅号的openid
class Vipuser extends Model{
    /*
     * 允许传入数组和非数组以验证是否存在，存在返回 true
     * */
    public function isExist($openidArr) {
        if(!is_array($openidArr)) {
            $openidArr=array(
                'openid'=>"$openidArr"
            );
        }
        if( $this->where($openidArr)->count() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    //新增数据
    public function addNew($arr) {
        if($this->insert($arr)){
            return true;
        }
        else{
            return false;
        }
    }

    //批量传入关联数组以获取基本信息，返回一条数据
    public function getInfoByArr($arr) {
        $res=$this->where($arr)->find();
        if(!empty($res['openid'])){
            return $res;
        }
        else{
            return null;
        }
    }

    public function getInfoForChangeBg($openid) {
        return $this->field("studentid,openid,code,name")->where("openid='$openid'")->find();
    }

    /*
     * 根据openid更新函数
     * 参数1：需要更改的用户openid
     * 参数2：待更新的关联数组
     * */
    public function updata($openid,$upArr) {
        if($this->where("`openid`='$openid'")->update($upArr) !== false) {
            return true;
        }
        else{
            return false;
        }
    }

    //删除用户
    public function deleteInfo($whereArr) {
        return $this->where($whereArr)->delete();
    }

    /*
     * 获取排名
     * 逻辑上我们按照vipuser表上的时间进行排名，
     * 但是isa表才有学号数据，xs表才有姓名
     * */
    public function getPageRank($start,$limit=20) {
        $whereStr = "`club_vipuser`.`openid` != 'o16hwwb9HjRJ9uxDHWqd4FoHdeFI' ";
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
            $whereStr.="and `club_vipuser`.`openid` != '$item' ";
        }
        $res = $this->field("club_user.headimgurl as headImgUrl,left(club_vipuser.studentid,4) as grade,club_vipuser.name")
            ->where($whereStr." and club_vipuser.addtime != 0 and club_vipuser.studentid != '' and club_vipuser.studentid is not null")
            ->join("club_user","club_user.openid=club_vipuser.openid","LEFT")
            ->order("club_vipuser.addtime asc")
            ->limit($start,$limit)
            ->select();
        return $res;
    }

    //"`club_isa.openid`='$openid'"
    public function getOneRank($openid) {
        $res = $this->field("club_user.headimgurl as headImgUrl,right(club_vipuser.studentid,4),club_vipuser.name")->join("club_user","club_user.openid='$openid' and club_user.openid = club_vipuser.openid")->find();
        return $res;
    }

    public function getRank($openid) {
        $res = $this->query("SELECT count(*) as cou FROM `club_vipuser` WHERE addtime < (SELECT `addtime` from club_vipuser where `openid`='$openid')");
//        file_put_contents("test.txt",json_encode($res).PHP_EOL,FILE_APPEND);
//        return 8;
        return $res[0]['cou']+1;
    }

}
