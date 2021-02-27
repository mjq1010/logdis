<?php

namespace app\model;

use think\Model;

class Waybill extends Model
{
    public function insertWaybill($dataArr)
    {
        return Waybill::create($dataArr); 
    }
    public function findWayInfo($page, $limit)
    {
        $infoCount = Waybill::where('bill_status', '运输中')->select()->count();;
        $pageInfo = Waybill::where('bill_status', '运输中')->page($page,$limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    public function updateWaybillStu($id)
    {
        return Waybill::where('bill_id',$id)->update(['bill_status'=>'完成']);
    }
    public function searchWaybill($dataArr)
    {
        $Field = ['bill_id', 'order_id','car_id','driver_id'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Waybill::where('bill_status', '运输中')->where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
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
