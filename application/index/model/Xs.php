<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/29
 * Time: 下午3:47
 */

namespace app\index\model;
use think\Model;

class Xs extends Model {

    public function getOne($array) {
        return $this->where($array)->find();
    }

    public function isExist($array) {
        $count = $this->where($array)->count();
        return $count>0;
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


}

