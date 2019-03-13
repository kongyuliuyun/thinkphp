<?php
namespace app\index\controller;
use think\Controller;
class Base extends Controller{
    public function _initialize()
    {
        if(!session('name')){
            $this->error('请登录','Index/index');
        }

    }
}
/**
 * Created by PhpStorm.
 * User: skyuniverse
 * Date: 2019/3/13
 * Time: 12:43
 */