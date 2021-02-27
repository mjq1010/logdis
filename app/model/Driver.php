<?php

namespace app\model;

use think\Model;

class Driver extends Model
{
    public function findDriverInfo($page, $limit)
    {
        $infoCount = Driver::field('driver_id')->select()->count();;
        $pageInfo = Driver::page($page, $limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function delDriver($id)
    {
        // 1.先查看司机状态看是否处于忙碌中;
        $find = Driver::where('driver_id', $id)->value('driver_status');
        if ($find == '空闲' || $find == '请假') {
            // 如果司机不是忙碌状态，则可以删除
            Driver::where('driver_id', $id)->delete();
            return true;
        } else {
            return false;
        }
    }
    public function delDriverAll($arrId)
    {
        $resArr = [];
        foreach ($arrId as $id) {
            $find = Driver::where('driver_id', $id)->value('driver_status');
            if ($find == '空闲' || $find == '请假') {
                Driver::where('driver_id', $id)->delete();
                array_push($resArr, $find);
            }
        }
        if ($resArr) {
            return true;
        } else {
            return false;
        }
    }
    public function insertDriver($dataArr)
    {
        // 1.先查找有没有目标司机;
        $find = Driver::where('driver_sid', $dataArr['driver_sid'])->find();
        // 2.如何返回数据不为空，则说明司机已存在，返回状态为false
        if ($find) {
            return false;
        } else {
            // 3.如果为空则，插入数据，返回状态为true；
            return Driver::create($dataArr);
        }
    }
    public function updataDriver($dataArr)
    {
        // 1.先查看司机状态看是否处于忙碌中;
        $find = Driver::where('driver_id', $dataArr['driver_id'])->value('driver_status');
        if ($find == '空闲' || $find == '请假') {
            Driver::where('driver_id', $dataArr['driver_id'])
                ->update([
                    'driver_name'    => $dataArr['driver_name'],
                    'driver_tel'     => $dataArr['driver_tel'],
                    'driver_status'  => $dataArr['driver_status'],
                ]);
            return true;
        } else {
            return false;
        }
    }
    // 返回司机名
    public function findDriverName()
    {
        return Driver::where('driver_status', '空闲')->column('driver_name', 'driver_id');
    }
    public function updateStatus($id)
    {
        Driver::where('driver_id', $id)->update(['driver_status' => '忙碌']);
    }
    public function updataStatusCom($id)
    {
        Driver::where('driver_id', $id)->update(['driver_status' => '空闲']);
    }
    // 统计司机状态
    public function CountStatus()
    {
        $statusArr = ["空闲", "忙碌", "请假"];
        $dataArr=[];
        foreach ($statusArr as $item) {
            $count=Driver::where('driver_status', $item)->count();
            array_push($dataArr, $count);
        }
        return $dataArr;
    }
    // 模糊查询
    public function searchDriver($dataArr)
    {
        $Field = ['driver_id', 'driver_name', 'driver_sid', 'driver_sex','driver_tel','driver_status'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Driver::where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
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
