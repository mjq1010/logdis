<?php

namespace app\model;

use think\Model;

class Admin extends Model
{
    public function findAdmin($dataArr)
    {
        // 1.先查找有没有目标管理员;
        $find = Admin::where('admin_name', $dataArr['admin_name'])->value('admin_password');
        // 2.如果返回数据为空，则说明管理员不存在，返回状态为false
        if (!$find) {
            return false;
        } else {
            // 3.如果不为空则，则判断密码是否正确，正确返回状态为true；否则返回false；
            if ($find == $dataArr['admin_password']) {
                return true;
            } else {
                return false;
            }
        }
    }
    // 查找管理员类型
    public function findAdminSort($id)
    {
        return Admin::where('admin_id', $id)->value('admin_role');
    }
    // 根据管理员名查询id
    public function findAdminId($admin_name)
    {
        $id = Admin::where('admin_name', $admin_name)->value('admin_id');
        return $id;
    }
    // 根据id查询管理员名
    public function findAdminName($admin_id)
    {
        $name = Admin::where('admin_id', $admin_id)->value('admin_name');
        return $name;
    }
    // 查询管理员信息；
    public function findAdminInfo($page, $limit)
    {
        $infoCount = Admin::field('admin_id,admin_name,admin_role,admin_describe')->select()->count();;
        $pageInfo = Admin::field('admin_id,admin_name,admin_role,admin_describe')->page($page, $limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    // 新增管理员；
    public function addAdmin($dataArr)
    {
        // 1.先查找有没有目标用户;
        $find = Admin::where('admin_name', $dataArr['admin_name'])->find();
        // 2.如何返回数据不为空，则说明用户名已存在，返回状态为false
        if ($find) {
            return false;
        } else {
            // 3.如果为空则，插入数据，返回状态为true；
            return Admin::create($dataArr);
        }
    }
    // 根据id单个删除对应管理员；
    public function delAdmin($id)
    {
        $res = Admin::where('admin_id', $id)->delete();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    // 根据id数组批量删除管理员；
    public function delAdminAll($arrId)
    {
        $resArr = [];
        foreach ($arrId as $id) {
            Admin::where('admin_id', $id)->delete();
            array_push($resArr, $id);
        }
        if ($resArr) {
            return true;
        } else {
            return false;
        }
    }
    // 更新管理员信息
    public function updataAdmin($dataArr)
    {
        // 1.先查找有没有目标用户;
        $find = Admin::where('admin_name', $dataArr['admin_name'])->value('admin_id');
        if ($find == $dataArr['admin_id'] || $find == null) {
            // 2.如果为空则更新数据，返回状态为true；
            Admin::where('admin_id', $dataArr['admin_id'])
                ->update([
                    'admin_name'     => $dataArr['admin_name'],
                    'admin_role'     => $dataArr['admin_role'],
                    'admin_describe' => $dataArr['admin_describe']
                ]);
            return true;
        } else {
            return false;
        }
    }
    // 修改密码
    public function updatePass($dataArr)
    {
        // 1.先查找有没有目标管理员;
        $find = Admin::where('admin_id', $dataArr['admin_id'])->value('admin_password');
        // 2.如果返回数据为空，则说明目标管理员不存在，返回状态为false
        if (!$find) {
            return false;
        } else {
            // 3.如果不为空则，则判断密码是否正确，正确返回状态为true；否则返回false；
            if ($find == $dataArr['oldPass']) {
                //  4.为真，则更新密码；
                Admin::where('admin_id', $dataArr['admin_id'])
                    ->update(['admin_password' => $dataArr['newPass']]);
                return true;
            } else {
                return false;
            }
        }
    }
    // 根据id返回管理员的部分数据
    public function findInfo($id)
    {
        // 1.先查找有没有目标用户;
        $find = Admin::where('admin_id', $id)->field('admin_name,admin_role,admin_describe')->find();
        if ($find) {
            // 存在返回数据
            return $find;
        } else {
            // 不存在返回false
            return false;
        }
    }
    // 更新管理员数据
    public function updataInfo($dataArr)
    {
        // 1.先查找有没有目标用户;
        $find = Admin::where('admin_name', $dataArr['admin_name'])->value('admin_id');
        if ($find == $dataArr['admin_id'] || $find == null) {
            // 2.如果为空则更新数据，返回状态为true；
            Admin::where('admin_id', $dataArr['admin_id'])
                ->update([
                    'admin_name'     => $dataArr['admin_name'],
                    'admin_describe' => $dataArr['admin_describe']
                ]);
            return true;
        } else {
            return false;
        }
    }
    // 统计管理员人数
    public function CountMan()
    {
        $authorArr = ["超级管理员", "配送管理员", "车辆管理员", "司机管理员", "运单管理员"];
        $dataArr=[];
        foreach ($authorArr as $item) {
            $count=Admin::where('admin_role', $item)->count();
            array_push($dataArr, $count);
        }
        return $dataArr;
    
    }
    // admin模糊查询
    public function searchAdmin($dataArr)
    {
        $Field = ['admin_id', 'admin_name', 'admin_role', 'admin_describe'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = Admin::where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
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
