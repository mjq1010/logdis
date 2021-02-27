<?php

namespace app\controller;

use app\BaseController;
use think\Response;
use app\model\User as UserModel;
use app\model\Order as OrderModel;

// 操作用户
class User extends BaseController
{
    // 构造函数
    public function __construct()
    {
        // 1.判断用户是否登录，只有用户登录了才能访问
        if (!cookie('?user_id')) {
            // 如果都没登陆则跳转到登录页，要写全路径，不然会造成路径混乱；
            redirect('http://www.logdis.com/index.php/login')->send();
        }
        // 定义公共模型
        $this->user_tb = new UserModel();
        $this->order_tb = new OrderModel();
    }

    // 加载用户首页
    public function index()
    {
        // 省略首页
        redirect('http://www.logdis.com/index.php/user/addOrder')->send();
    }

    // 加载用户修改密码页面
    public function repass()
    {
        return view();
    }
    // 接收更改密码请求
    public function repassTo()
    {
        $status = $this->user_tb->updatePass([
            'user_id' => cookie('user_id'),
            'oldPass' => md5(input('oldPass')),
            'newPass' => md5(input('newPass'))
        ]);
        if ($status) {
            // 删除原有cookie
            cookie('user_id', null);
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    // 加载用户修改资料页面
    public function info()
    {
        return view();
    }
    // 加载资料数据；
    public function getInfo()
    {
        $status = $this->user_tb->findInfo(cookie('user_id'));
        if ($status) {
            return createApi('数据获取成功', $status);
        } else {
            return createApi('数据获取失败', $status);
        }
    }
    // 响应修改资料
    public function infoTo()
    {
        $status = $this->user_tb->updataUser([
            'user_id'      => cookie('user_id'),
            'user_name'    => input('user_name'),
            'user_tel'     => input('user_tel'),
            'user_address' => input('user_address'),
        ]);
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    // 加载添加订单页面
    public function addOrder()
    {
        $status = $this->user_tb->findUserTel(cookie('user_id'));
        if ($status) {
            // 如果设置了手机号，则渲染添加订单页面；
            return view();
        } else {
            // 如果没有设置手机号，则渲染信息页面；
            return redirect('http://www.logdis.com/index.php/user/info');
        }
    }
    // 响应添加订单
    public function addOrderTo()
    {
        // 获取发货地址；
        $find = $this->user_tb->findInfo(cookie('user_id'));
        $status = $this->order_tb->insertOrder([
            'user_id'   => cookie('user_id'),
            'type'      => input('type'),
            'method'    => input('method'),
            'weight'    => input('weight'),
            'price'     => input('price'),
            'f_name'    => $find['user_name'],
            'f_phone'   => $find['user_tel'],
            'f_address' => implode(explode("|", $find['user_address'])),
            's_name'    => input('s_name'),
            's_phone'   => input('s_phone'),
            's_address' => input('s_address'),
            'status'    => input('status'),
        ]);
        if ($status) {
            return createApi('数据添加成功', $status);
        } else {
            return createApi('数据添加失败', $status);
        }
    }

    // 加载查看订单页面；
    public function lookOrder()
    {
        return view();
    }
    public function getOrder()
    {
        if (input('data')) {
            $status = $this->order_tb->searchOrder(input(), cookie('user_id'));
        } else {
            $status = $this->order_tb->findOrderById(input('page'), input('limit'), cookie('user_id'));
        }
        if ($status) {
            $result = [
                'code' => 0,
                'msg'  => "数据获取成功",
                'count' => $status['count'],
                'data' => $status['data']
            ];
            // 返回api接口
            return Response::create($result, 'json');
        } else {
            return createApi('暂无数据', $status);
        }
    }
    public function orderDel()
    {
        $status = $this->order_tb->delOrder(input('order_id'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }
    public function orderDelAll()
    {
        $status = $this->order_tb->delOrderAll(input('arrId'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }

    // 接收获取用户名请求
    public function getName()
    {
        $status = $this->user_tb->findUserName(cookie('user_id'));
        if ($status) {
            return createApi('数据获取成功', $status);
        } else {
            return createApi('数据获取失败', $status);
        }
    }
    // 接收获取地址请求
    public function getAddress()
    {
        $status = $this->user_tb->findUserAddress(cookie('user_id'));
        if ($status) {
            return createApi('数据获取成功', $status);
        } else {
            return createApi('数据获取失败', $status);
        }
    }
    // 接收用户注销请求；
    public function close()
    {
        // 1.清除相对应的cookie
        cookie('user_id', null);
        // 2.返回状态；
        return createApi('注销成功', true);
    }
}
