<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/29
 * Time: 下午4:59
 * 以后业务复杂可以使用 23333
 */

namespace app\index\validate;
use think\validate;

class User extends validate {

    protected $rule = [
        'name'  =>  'require|max:25|token',
    ];

    protected $message  =   [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',
    ];


}



