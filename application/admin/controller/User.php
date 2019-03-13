<?php
namespace app\admin\controller;
use \app\admin\controller\Base as BaseController;
use app\admin\model\User as UserModel;
use app\admin\validate\User as UserValidate;


class User extends  BaseController{
    public function index(){
        return  $this->fetch();
    }
    public function userlist(){
        $data=UserModel::paginate(3);
        $this->assign('data',$data);
        $page=$data->render();
        $this->assign('page',$page);
        return  $this->fetch();
    }
    public function add(){
        return  $this->fetch();
    }
    //新增管理员方法
    public function insert(){
        $data=input('post.');
        $val=new UserValidate();
        if(!$val->check($data)){
             $this->error($val->getError());
            exit();
        }
        $user=new UserModel($data);
        $ret=$user->allowField(true)->save();


        if($ret){
            $this->success('新增管理员成功','User/userlist');
        }
        else{
            $this->error('添加管理员失败');
        }

    }
    public function edit(){
        $id=input('get.id');
        $data=UserModel::get($id);
        $this->assign('data',$data);

        return  $this->fetch();
    }
    public function update(){
        $data=input('post.');
        $id=input('post.id');
        $val=new UserValidate();
        if(!$val->check($data)){
            $this->error($val->getError());
            exit();
        }
        $user=new UserModel();
        $ret=$user->allowField(true)->save($data,['id'=>$id]);
        if($ret){
            $this->success('更新成功','User/userlist');
        }
        else{
            $this->error('修改失败');
        }
    }
    public function delete(){
        $id=input('get.id');
        $user=UserModel::destroy($id);
        if($user){
            $this->success('删除成功','User/userlist');
        }else{
            $this->error('删除失败');
        }
    }
    public  function upload(){

        return $this->fetch();

    }
    public function toUpload(){
        $file = request()->file('image');
//        $id=input('post.id');
//        $user=new UserModel();

        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                $type=$info->getExtension();

                $mkdir= $info->getSaveName();

                $fil= $info->getFilename();
                $this->success("上传成功");
            }else{
                // 上传失败获取错误信息
                echo $file->getError();

            }
        }else{
            $this->error('请传入文件');
        }
    }
}
