<?php

namespace app\controller;
// 引入模型
use app\model\Admin as AdminModel;
use app\model\User as UserModel;
use think\facade\Cookie;

class Login
{
    public function index()
    {
        // 加载登录页面
        return view();
    }
    // 接收管理员登录请求
    public function adminLogin()
    {
        $admin_tb = new AdminModel();
        $status =  $admin_tb->findAdmin([
            'admin_name' => input('admin_name'),
            'admin_password' => md5(input('admin_password')),
        ]);
        if ($status) {
            $id = $admin_tb->findAdminId(input('admin_name'));
            // 验证通过设置session
            session('admin_id', $id);
            return createApi('管理员验证成功', $status);
        } else {
            return createApi('管理员验证失败', $status);
        }
    }
    // 接收用户登录请求
    public function userLogin()
    {
        $user_tb = new UserModel();
        $status =  $user_tb->findUser([
            'user_name' => input('user_name'),
            'user_password' => md5(input('user_password'))
        ]);
        if ($status) {
            $id = $user_tb->findUserId(input('user_name'));
            // 验证通过设置cookie,根据有无token，设置cookie的时长
            if (input('token') == "true") {
                Cookie::forever('user_id', $id);
            } else {
                cookie('user_id', $id);
            }
            return createApi('用户验证成功', $status);
        } else {
            return createApi('用户验证失败', $status);
        }
    }
}
