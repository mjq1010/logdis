<?php

namespace app\model;

use think\Model;

class Car extends Model
{
    public function findCarInfo($page, $limit)
    {
        $infoCount = Car::field('car_id')->select()->count();;
        $pageInfo = Car::page($page, $limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function delCar($id)
    {
        // 1.先查看车辆状态看是否处于忙碌中;
        $find = Car::where('car_id', $id)->value('car_status');
        if ($find == '空闲' || $find == '维修') {
            // 如果车辆不是忙碌状态，则可以删除
            Car::where('car_id', $id)->delete();
            return true;
        } else {
            return false;
        }
    }
    public function delCarAll($arrId)
    {
        $resArr = [];
        foreach ($arrId as $id) {
            $find = Car::where('car_id', $id)->value('car_status');
            if ($find == '空闲' || $find == '维修') {
                Car::where('car_id', $id)->delete();
                array_push($resArr, $find);
            }
        }
        if ($resArr) {
            return true;
        } else {
            return false;
        }
    }
    public function addCar($dataArr)
    {
        // 1.先查找有没有目标车辆;
        $find = Car::where('car_num', $dataArr['car_num'])->find();
        // 2.如何返回数据不为空，则说明车辆已存在，返回状态为false
        if ($find) {
            return false;
        } else {
            // 3.如果为空则，插入数据，返回状态为true；
            return Car::create($dataArr);
        }
    }
    public function updataCar($dataArr)
    {
        // 1.先查找有没有目标车辆;
        $find = Car::where('car_num', $dataArr['car_num'])->value('car_id');
        if ($find == $dataArr['car_id'] || $find == null) {
            // 2.如果为空则更新数据，返回状态为true；
            Car::where('car_id', $dataArr['car_id'])
                ->update([
                    'car_num'     => $dataArr['car_num'],
                    'car_sort'    => $dataArr['car_sort'],
                    'car_weight'  => $dataArr['car_weight'],
                    'car_status'  => $dataArr['car_status'],
                ]);
            return true;
        } else {
            return false;
        }
    }
    // 返回车牌号
    public function findCarName()
    {
        return Car::where('car_status', '空闲')->column('car_num', 'car_id');
    }
    public function updateStatus($id)
    {
        Car::where('car_id', $id)->update(['car_status' => '忙碌']);
    }
    public function updataStatusCom($id)
    {
        Car::where('car_id', $id)->update(['car_status' => '空闲']);
    }
    // 统计车辆状态
    public function CountStatus()
    {
        $statusArr = ["空闲", "忙碌", "维修"];
        $dataArr = [];
        foreach ($statusArr as $item) {
            $count = Car::where('car_status', $item)->count();
            array_push($dataArr, $count);
        }
        return $dataArr;
    }
    // 模糊查询
    public function searchCar($dataArr)
    {
        $Field = ['car_id', 'car_num', 'car_sort', 'car_weight', 'car_status'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Car::where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
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
