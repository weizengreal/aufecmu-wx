<?php
/**
 * Created by PhpStorm.
 * User: ancai4399
 * Date: 2016/12/18
 * Time: 12:50
 */

namespace app\index\model;
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


    /*
     * 根据openid更新函数
     * 参数1：需要更改的用户openid
     * 参数2：待更新的关联数组
     * */
    public function updata($openid,$upArr) {
//        file_put_contents("test.txt",$this->fetchSql()->where("`openid`='$openid'")->update($upArr).PHP_EOL,FILE_APPEND);
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
     * 获取用户领卡排名
     * */
    public function getRank($openid) {
        $res = $this->query("SELECT count(*) as cou FROM `club_vipuser` WHERE gettime < (SELECT `gettime` from club_vipuser where `openid`='$openid')");
//        file_put_contents("test.txt",json_encode($res).PHP_EOL,FILE_APPEND);
//        return 8;
        return $res[0]['cou']+1;
    }


}
