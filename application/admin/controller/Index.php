<?php
namespace app\admin\controller;
use think\Controller;
use  app\admin\model\User as UserModel;
class Index extends Controller{
    public function login(){
        return $this->fetch();
    }
    public function check(){
        $data=input('post.');
        $user=new UserModel();

        $ret=$user->where('name',$data['name'])->find();
        if($ret){
            if($ret['password']===md5($data['password'])){
                session('name',$data['name']);
            }else{
                $this->error('用户密码错误');
            }

        }else{
            $this->error('用户名不存在');
            exit();
        }
        if(captcha_check($data['code'])){
            $this->success('登录成功','User/index');
        }else{
            $this->error('验证码错误');
        }
    }
    public function logout(){
        $ret=session(null);
        $this->success('退出登录','Index/login');
    }

}
/**
 * Created by PhpStorm.
 * User: skyuniverse
 * Date: 2019/3/13
 * Time: 9:59
 */