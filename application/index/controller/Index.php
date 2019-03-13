<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Person as PersonModel;
use app\index\validate\Person as PersonValidate;


class Index  extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function login()
    {
        return $this->fetch();
    }
    public function register()
    {
        return $this->fetch();
    }
    public  function add(){
        $data=input('post.');

        $val=new PersonValidate();
        if(!$val->check($data)){
            $this->error($val->getError());
            exit();
        }

        $user=new PersonModel($data);
        $ret=$user->allowField(true)->save();
        if($ret){
            $this->success('注册成功','Index/index');
        }
        else{
            $this->error('注册失败');
        }
    }
    public  function check(){
        $data=input('post.');
        $user=new PersonModel();
        $ret=$user->where('name',$data['name'])->find();
        if($ret){
            if($data['password']===$ret['password']){
                session('name');
                $this->success('用户登录成功','Index/index');
            }else{
                $this->error('密码错误');
            }
        }else{
            $this->error('用户名不存在');
        }
    }
    public function logout(){
        session(null);
        $this->success('退出登录','Index/login');
    }
}
