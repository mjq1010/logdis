<?php

namespace app\controller;

use app\BaseController;
use think\Response;
use app\model\Admin as AdminModel;
use app\model\User as UserModel;
use app\model\Car as CarModel;
use app\model\Driver as DriverModel;
use app\model\Order as OrderModel;
use app\model\Waybill as WaybillModel;

class Index extends BaseController
{
    public function __construct()
    {
        // 1.判断用户或管理员是否登录
        if (!cookie('?user_id') && !session('?admin_id')) {
            // 如果都没登陆则跳转到登录页，要写全路径，不然会造成路径混乱；
            redirect('http://www.logdis.com/index.php/login')->send();
        }
        // 2.如果用户cookie存在则优先登录到用户首页
        if (cookie('?user_id')) {
            redirect('http://www.logdis.com/index.php/user')->send();
        }
        // 定义公共模型
        $this->admin_tb = new AdminModel();
        $this->user_tb = new UserModel();
        $this->car_tb = new CarModel();
        $this->driver_tb = new DriverModel();
        $this->order_tb = new OrderModel();
        $this->waybill_tb = new WaybillModel();
    }
    // 加载首页面
    public function index()
    {
        return view();
    }
    public function author()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='超级管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }
        return view();
    }
    // 权限管理中对管理员的删除;
    public function authorDel()
    {
        // 管理员不能删除自己
        if (session('admin_id') == input('admin_id')) {
            return createApi('数据删除失败', false);
        } else {
            $status = $this->admin_tb->delAdmin(input('admin_id'));
            if ($status) {
                return createApi('数据删除成功', $status);
            } else {
                return createApi('数据删除失败', $status);
            }
        }
    }
    // 权限管理中对管理员的批量删除;
    public function authorDelAll()
    {
        $arrId = [];
        foreach (input('arrId') as $id) {
            // 过滤当前管理员；
            if (session('admin_id') != $id) {
                array_push($arrId,  $id);
            }
        }
        if ($arrId) {
            $status = $this->admin_tb->delAdminAll($arrId);
            if ($status) {
                return createApi('数据删除成功', $status);
            } else {
                return createApi('数据删除失败', $status);
            }
        } else {
            // 数组为空说明删除的是当前管理员
            return createApi('数据删除失败', false);
        }
    }

    // 添加管理员
    public function addAuthor()
    {
        $status = $this->admin_tb->addAdmin([
            'admin_name'     => input('admin_name'),
            'admin_password' => md5(input('admin_password')),
            'admin_role'     => input('admin_role'),
            'admin_describe' => input('admin_describe')
        ]);
        if ($status) {
            return createApi('数据增加成功', $status);
        } else {
            return createApi('数据增加失败', $status);
        }
    }
    // 更新管理员信息
    public function EditAuthor()
    {
        $status = $this->admin_tb->updataAdmin([
            'admin_id'       => input('admin_id'),
            'admin_name'     => input('admin_name'),
            'admin_role'     => input('admin_role'),
            'admin_describe' => input('admin_describe'),
        ]);
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    // 获取管理员所有信息(除了密码);
    public function getInfo()
    {
        if (input('data')) {
            $status = $this->admin_tb->searchAdmin(input());
        } else {
            $status = $this->admin_tb->findAdminInfo(input('page'), input('limit'));
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

    // 加载车辆管理页面
    public function car()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='车辆管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }
        return view();
    }
    // 获取所有车辆信息
    public function getCarInfo()
    {
        if (input('data')) {
            $status = $this->car_tb->searchCar(input());
        } else {
            $status = $this->car_tb->findCarInfo(input('page'), input('limit'));
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
    public function carDel()
    {
        $status = $this->car_tb->delCar(input('car_id'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }
    public function carDelAll()
    {
        $status = $this->car_tb->delCarAll(input('arrId'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }
    public function addCar()
    {
        $status = $this->car_tb->addCar(input());
        if ($status) {
            return createApi('数据增加成功', $status);
        } else {
            return createApi('数据增加失败', $status);
        }
    }
    public function editCar()
    {
        $status = $this->car_tb->updataCar(input());
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }

    // 加载司机管理页面
    public function driver()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='司机管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }
        return view();
    }
    public function getDriverInfo()
    {
        if (input('data')) {
            $status = $this->driver_tb->searchDriver(input());
        } else {
            $status = $this->driver_tb->findDriverInfo(input('page'), input('limit'));
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
    public function driverDel()
    {
        $status = $this->driver_tb->delDriver(input('driver_id'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }
    public function driverDelAll()
    {
        $status = $this->driver_tb->delDriverAll(input('arrId'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }
    public function addDriver()
    {
        $status = $this->driver_tb->insertDriver(input());
        if ($status) {
            return createApi('数据增加成功', $status);
        } else {
            return createApi('数据增加失败', $status);
        }
    }
    public function editDriver()
    {
        $status = $this->driver_tb->updataDriver(input());
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }

    // 客户管理页面
    public function client()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='超级管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }
        return view();
    }
    // 请求数据
    public function getClient()
    {
        if (input('data')) {
            $status = $this->user_tb->searchClient(input());
        } else {
            $status = $this->user_tb->findUserAll(input('page'), input('limit'));
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
    // 批量删除用户
    public function clientDelAll()
    {
        $status = $this->user_tb->delClientAll(input('arrId'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }
    // 单个删除客户
    public function clientDel()
    {
        $status = $this->user_tb->delClient(input('user_id'));
        if ($status) {
            return createApi('数据删除成功', $status);
        } else {
            return createApi('数据删除失败', $status);
        }
    }
    // 配送管理/审核揽收
    public function collect()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='配送管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }
        return view();
    }
    public function getCollect()
    {
        if (input('data')) {
            $status = $this->order_tb->searchOrderCol(input());
        } else {
            $status = $this->order_tb->findOrderCol(input('page'), input('limit'));
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
    // 单个揽收
    public function collectTo()
    {
        $status = $this->order_tb->updataStatusCol(input('order_id'));
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    // 批量揽收
    public function collectToAll()
    {
        $status = $this->order_tb->updataStatusColAll(input('arrId'));
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    // 拒接
    public function collectRe()
    {
        $status = $this->order_tb->updataStatusColRe(input());
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    public function delivery()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='配送管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }
        return view();
    }
    public function getDelivery()
    {
        if (input('data')) {
            $status = $this->order_tb->searchOrderDeli(input());
        } else {
            $status = $this->order_tb->findOrderDeli(input('page'), input('limit'));
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
    public function deliveryTo()
    {
        $status = $this->order_tb->updataStatusDeli(input('order_id'));
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    public function deliveryToAll()
    {
        $status = $this->order_tb->updataStatusDeliAll(input('arrId'));
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    public function waybill()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='运单管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }

        return view();
    }
    public function getWaybill()
    {
        if (input('data')) {
            $status = $this->order_tb->searchOrderWay(input());
        } else {
            $status = $this->order_tb->findOrderWay(input('page'), input('limit'));
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
    public function addWaybill()
    {
        $status = $this->waybill_tb->insertWaybill(input());
        // // 如果数据插入成功，更新状态
        if ($status) {
            $this->order_tb->updateStatus(input('order_id'));
            $this->car_tb->updateStatus(input('car_id'));
            $this->driver_tb->updateStatus(input('driver_id'));
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    public function getDriverCar()
    {
        $carArr = $this->car_tb->findCarName();
        $driverArr = $this->driver_tb->findDriverName();
        if ($carArr && $driverArr) {
            return createApi('数据获取成功', ['carArr' => $carArr, 'driverArr' => $driverArr]);
        } else {
            if (!$carArr) {
                return createApi('数据获取失败', 1);
            } else {
                return createApi('暂无数据', 2);
            }
        }
    }
    public function updateWaybill()
    {
        // $find=$this->admin_tb->findAdminSort(session('admin_id'));
        // if($find=='运单管理员'){
        //     return view();
        // }else{
        //     return view('error');
        // }

        return view();
    }
    public function getWaybillInfo()
    {
        if (input('data')) {
            $status = $this->waybill_tb->searchWaybill(input());
        } else {
            $status = $this->waybill_tb->findWayInfo(input('page'), input('limit'));
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
    public function waybillCom()
    {
        $status = $this->waybill_tb->updateWaybillStu(input('bill_id'));
        if ($status) {
            $this->order_tb->updataStatusCom(input('order_id'));
            $this->car_tb->updataStatusCom(input('car_id'));
            $this->driver_tb->updataStatusCom(input('driver_id'));
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }

    // 首页图表1数据
    public function getEchart1Data()
    {
        $authorArr = $this->admin_tb->CountMan();
        $user = $this->user_tb->CountMan();
        array_push($authorArr, $user);
        return $authorArr;
    }
    // 首页图表2数据
    public function getEchart2Data()
    {
        return $this->order_tb->CountStatus();
    }
    // 首页图表3数据
    public function getEchart3Data()
    {
        return $this->car_tb->CountStatus();
    }
    // 首页图表4数据
    public function getEchart4Data()
    {
        return $this->driver_tb->CountStatus();
    }
    // 首页图表5数据
    public function getEchart5Data()
    {
        return $this->order_tb->CountSort();
    }
    // 首页图表6数据
    public function getEchart6Data()
    {
        return $this->order_tb->CountMethod();
    }

    // 公共处理
    // 接收获取管理员名请求
    public function getName()
    {
        $status = $this->admin_tb->findAdminName(session('admin_id'));
        if ($status) {
            return createApi('数据获取成功', $status);
        } else {
            return createApi('数据获取失败', $status);
        }
    }
    // 加载管理员修改资料页面
    public function info()
    {
        return view();
    }
    // 获取展示基本资料
    public function showInfo()
    {
        $status = $this->admin_tb->findInfo(session('admin_id'));
        if ($status) {
            return createApi('数据获取成功', $status);
        } else {
            return createApi('数据获取失败', $status);
        }
    }
    // 响应修改资料
    public function infoTo()
    {
        $status = $this->admin_tb->updataInfo([
            'admin_id'       => session('admin_id'),
            'admin_name'     => input('admin_name'),
            'admin_describe' => input('admin_describe'),
        ]);
        if ($status) {
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    // 加载管理员修改密码页面
    public function repass()
    {
        return view();
    }
    // 接收更改密码请求
    public function repassTo()
    {
        $status = $this->admin_tb->updatePass([
            'admin_id' => session('admin_id'),
            'oldPass' => md5(input('oldPass')),
            'newPass' => md5(input('newPass'))
        ]);
        if ($status) {
            // 删除原有cookie
            session('admin_id', null);
            return createApi('数据更新成功', $status);
        } else {
            return createApi('数据更新失败', $status);
        }
    }
    // 接收管理员注销请求；
    public function close()
    {
        // 1.清除相对应的session
        session('admin_id', null);
        // 2.返回状态；
        return createApi('注销成功', true);
    }
    // 错误提示页面
    public function error()
    {
        return view();
    }
}
