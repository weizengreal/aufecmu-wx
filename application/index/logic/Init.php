<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/30
 * Time: 下午2:50
 */
namespace app\index\logic;

class Init {
    private $isa;
    private $xs;

    public function __construct() {
        $this->isa = new  \app\index\model\Isa();
        $this->xs = new  \app\index\model\Xs();
    }



}

