<?php

namespace app\model;

use think\Model;

// 主要负责对用户表的操作；
class User extends Model
{
    public function addUser($dataArr)
    {
        // 1.先查找有没有目标用户;
        $find = User::where('user_name', $dataArr['user_name'])->find();
        // 2.如何返回数据不为空，则说明用户名已存在，返回状态为false
        if ($find) {
            return false;
        } else {
            // 3.如果为空则，插入数据，返回状态为true；
            return User::create($dataArr);
        }
    }

    // 验证登录
    public function findUser($dataArr)
    {
        // 1.先查找有没有目标用户;
        $find = User::where('user_name', $dataArr['user_name'])->value('user_password');
        // 2.如果返回数据为空，则说明用户不存在，返回状态为false
        if (!$find) {
            return false;
        } else {
            // 3.如果不为空则，则判断密码是否正确，正确返回状态为true；否则返回false；
            if ($find == $dataArr['user_password']) {
                return true;
            } else {
                return false;
            }
        }
    }
    // 根据id查询用户名
    public function findUserName($user_id)
    {
        $name = User::where('user_id', $user_id)->value('user_name');
        return $name;
    }
    // 根据用户名查询id
    public function findUserId($user_name)
    {
        $id = User::where('user_name', $user_name)->value('user_id');
        return $id;
    }
    // 修改密码
    public function updatePass($dataArr)
    {
        // 1.先查找有没有目标用户;
        $find = User::where('user_id', $dataArr['user_id'])->value('user_password');
        // 2.如果返回数据为空，则说明用户不存在，返回状态为false
        if (!$find) {
            return false;
        } else {
            // 3.如果不为空则，则判断密码是否正确，正确返回状态为true；否则返回false；
            if ($find == $dataArr['oldPass']) {
                //  4.为真，则更新密码；
                User::where('user_id', $dataArr['user_id'])
                    ->update(['user_password' => $dataArr['newPass']]);
                return true;
            } else {
                return false;
            }
        }
    }
    // 根据id返回用户的部分数据
    public function findInfo($id)
    {
        // 1.先查找有没有目标用户;
        $find = User::where('user_id', $id)->field('user_name,user_tel,user_address')->find();
        if ($find) {
            // 存在返回数据
            return $find;
        } else {
            // 不存在返回false
            return false;
        }
    }
    // 根据id返回用户的手机号
    public function findUserTel($id)
    {
        // 1.先查找有没有目标用户;
        $find = User::where('user_id', $id)->value('user_tel');
        if ($find) {
            // 存在返回数据
            return $find;
        } else {
            // 不存在返回false
            return false;
        }
    }
    // 根据id返回用户的地址；
    public function findUserAddress($id)
    {
        // 1.先查找有没有目标用户;
        $find = User::where('user_id', $id)->value('user_address');
        if ($find) {
            // 存在返回数据
            return $find;
        } else {
            // 不存在返回false
            return false;
        }
    }
    // 更新用户表
    public function updataUser($dataArr)
    {
        // 1.先查找有没有目标用户;
        $find = User::where('user_name', $dataArr['user_name'])->value('user_id');
        if ($find == $dataArr['user_id'] || $find == null) {
            // 2.如果为空则更新数据，返回状态为true；
            User::where('user_id', $dataArr['user_id'])
                ->update([
                    'user_name'    => $dataArr['user_name'],
                    'user_tel'     => $dataArr['user_tel'],
                    'user_address' => $dataArr['user_address']
                ]);
            return true;
        } else {
            return false;
        }
    }
    // 返回所有用户
    public function findUserAll($page, $limit)
    {
        $infoCount = User::field('user_id')->select()->count();
        $pageInfo = User::field('user_id,user_name,user_tel,user_address')->page($page, $limit)->select();
        if ($infoCount) {
            return [
                'count' => $infoCount,
                'data'  => $pageInfo
            ];
        } else {
            return false;
        }
    }
    // 客户管理，批量删除客户
    public function delClientAll($arrId)
    {
        $resArr = [];
        foreach ($arrId as $id) {
            User::where('user_id', $id)->delete();
            array_push($resArr, $id);
        }
        if ($resArr) {
            return true;
        } else {
            return false;
        }
    }
    // 单个删除客户
    public function delClient($id)
    {
        $res = User::where('user_id', $id)->delete();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    // 统计用户人数
    public function CountMan()
    {
        return User::select()->count();
    }
    // 模糊查询
    public function searchClient($dataArr)
    {
        $Field = ['user_id', 'user_name', 'user_tel', 'user_address'];
        $pageInfo = [];
        foreach ($Field as $item) {
            $find = User::where($item, 'like', '%' . $dataArr['data'] . '%')->page($dataArr['page'], $dataArr['limit'])->select();
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
