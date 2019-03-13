<?php
namespace app\admin\controller;
use  think\Controller;
class Base extends Controller{
    protected function _initialize()
{
    if(!session('name')){
        $this->error('请登录','Index/login');
    }
}
}
/**
 * Created by PhpStorm.
 * User: skyuniverse
 * Date: 2019/3/13
 * Time: 11:03
 */