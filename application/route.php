<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//设置社区权限，非静默授权
Route::rule("xyq","home/index/index");

// 微信分享中转部分
Route::rule("share","home/index/share");

//激活界面 静默授权
Route::rule("active","home/index/active");

//排名页面 非静默收起那
Route::rule("rank","home/xyq/rank");

//更换背景 静默授权
Route::rule("change","home/xyq/cardget");

// 微信端全局处理接口
Route::rule("interface","home/cardinterface/index");

// 开放接口用于调试
Route::rule("test","home/test/index");
