<?php

namespace app\controller;

// 引入模型
use app\model\User as UserModel;

class Register
{
    public function index()
    {
        // 加载注册页面
        return view();
    }
    // 接收添加用户请求;
    public function add()
    {
        // 使用user模型操作user表；
        $user_tb = new UserModel();
        // 获取前端发送的参数；
        $user_name = input('user_name');
        $user_password = input('user_password');
        // 向模型发送数据，然后拿到模型处理后的数据，发送到前端；
        $status = $user_tb->addUser([
            'user_name' => $user_name,
            'user_password' => md5($user_password)
        ]);
        // 返回前端数据
        if ($status) {
            return createApi('用户注册成功', $status);
        } else {
            return createApi('用户注册失败', $status);
        }
    }
}
