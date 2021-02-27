<?php

namespace app\model;

use think\Model;

// 主要负责对订单表的操作；
class Order extends Model
{
    // 插入订单数据
    public function insertOrder($dataArr)
    {
        $status = Order::create($dataArr);
        return $status;
    }
    public function findOrderById($page, $limit, $id)
    {
        $infoCount = Order::where('user_id', $id)->select()->count();;
        $pageInfo = Order::page($page, $limit)->where('user_id', $id)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function findOrderCol($page, $limit)
    {
        $infoCount = Order::where('status', '审核中')->select()->count();;
        $pageInfo = Order::where('status', '审核中')->page($page, $limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function updataStatusCol($id)
    {
        return  Order::where('order_id', $id)->update(['status' => '已揽收']);
    }
    public function updataStatusColAll($arrId)
    {
        $resArr = [];
        foreach ($arrId as $id) {
            Order::where('order_id', $id)->update(['status' => '已揽收']);
            array_push($resArr, $id);
        }
        if ($resArr) {
            return true;
        } else {
            return false;
        }
    }
    public function updataStatusColRe($dataArr)
    {
        return  Order::where('order_id', $dataArr['order_id'])
            ->update([
                'status' => '已拒接',
                'remark' => $dataArr['remark']
            ]);
    }
    public function findOrderDeli($page, $limit)
    {
        $infoCount = Order::where('status', '派件中')->select()->count();;
        $pageInfo = Order::where('status', '派件中')->page($page, $limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function updataStatusDeli($id)
    {
        return  Order::where('order_id', $id)->update(['status' => '已送达']);
    }
    public function updataStatusCom($id)
    {
        return  Order::where('order_id', $id)->update(['status' => '派件中']);
    }
    public function updataStatusDeliAll($arrId)
    {
        $resArr = [];
        foreach ($arrId as $id) {
            Order::where('order_id', $id)->update(['status' => '已送达']);
            array_push($resArr, $id);
        }
        if ($resArr) {
            return true;
        } else {
            return false;
        }
    }
    public function findOrderWay($page, $limit)
    {
        $infoCount = Order::where('status', '已揽收')->select()->count();;
        $pageInfo = Order::where('status', '已揽收')->page($page, $limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function updateStatus($id)
    {
        Order::where('order_id', $id)->update(['status' => '运输中']);
    }
    // 统计各订单的状态
    public function CountStatus()
    {
        $statusArr = ["已送达", "派件中", "运输中", "已揽收", "审核中"];
        $dataArr = [];
        $all = 0;
        foreach ($statusArr as $item) {
            $count = Order::where('status', $item)->count();
            $all += $count;
            array_push($dataArr, $count);
        }
        array_push($dataArr, $all);
        return $dataArr;
    }
    // 统计货物类型
    public function CountSort()
    {
        $statusArr = ["文件", "数码产品", "日用品", "服饰", "食品", "其他"];
        $dataArr = [];
        foreach ($statusArr as $item) {
            $count = Order::where('type', $item)->count();
            array_push($dataArr, ['name' => $item, 'value' => $count]);
        }
        return $dataArr;
    }
    // 统计订单方式
    public function CountMethod()
    {
        $statusArr = ["上门寄件", "代收点寄件"];
        $dataArr = [];
        foreach ($statusArr as $item) {
            $count = Order::where('method', $item)->count();
            array_push($dataArr, ['name' => $item, 'value' => $count]);
        }
        return $dataArr;
    }
    // 删除订单
    public function delOrder($id)
    {
        // 1.先查看订单状态看是否处于已送达;
        $find = Order::where('order_id', $id)->value('status');
        if ($find == '已送达' || $find == '已拒接') {
            // 如果订单是已送达已拒接状态，则可以删除
            Order::where('order_id', $id)->delete();
            return true;
        } else {
            return false;
        }
    }
    public function delOrderAll($arrId)
    {
        $resArr = [];
        foreach ($arrId as $id) {
            $find = Order::where('order_id', $id)->value('status');
            if ($find == '已送达' || $find == '已拒接') {
                Order::where('order_id', $id)->delete();
                array_push($resArr, $find);
            }
        }
        if ($resArr) {
            return true;
        } else {
            return false;
        }
    }
    // 模糊搜索
    public function searchOrder($dataArr, $id)
    {
        $Field = ['order_id', 'user_id', 'type', 'method', 'f_name', 'f_phone', 'f_address', 's_name', 's_phone', 's_address', 'status'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Order::where('user_id', $id)->where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
            // 如果查询结果不为0则返回该结果
            if (count($find) > 0) {
                $pageInfo = $find;
            }
        }
        $infoCount = count($pageInfo);
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function searchOrderCol($dataArr)
    {
        $Field = ['order_id', 'user_id', 'type', 'weight', 'price', 'method', 'f_name', 'f_phone', 'f_address', 's_name', 's_phone', 's_address', 'status'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Order::where('status', '审核中')->where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
            // 如果查询结果不为0则返回该结果
            if (count($find) > 0) {
                $pageInfo = $find;
            }
        }
        $infoCount = count($pageInfo);
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function searchOrderDeli($dataArr)
    {
        $Field = ['order_id', 'user_id', 'type', 'weight', 'price', 'method', 'f_name', 'f_phone', 'f_address', 's_name', 's_phone', 's_address', 'status'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Order::where('status', '派件中')->where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
            // 如果查询结果不为0则返回该结果
            if (count($find) > 0) {
                $pageInfo = $find;
            }
        }
        $infoCount = count($pageInfo);
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function searchOrderWay($dataArr)
    {
        $Field = ['order_id', 'user_id', 'type', 'weight', 'price', 'method', 'f_name', 'f_phone', 'f_address', 's_name', 's_phone', 's_address', 'status'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Order::where('status', '已揽收')->where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
            // 如果查询结果不为0则返回该结果
            if (count($find) > 0) {
                $pageInfo = $find;
            }
        }
        $infoCount = count($pageInfo);
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
}
