<?php

use think\Response;
// 应用公共文件
/**
 * 创建Api接口方法
 * @param  array        $data     数据
 * @param  string       $msg      提示信息
 * @param  int          $code     状态码
 * @param  string       $type     发送类型
 */
function createApi($msg = '', $data, $code = 200, $type = 'json')
{
    //  标准api结构生成
    $result = [
        'code' => $code,
        'msg'  => $msg,
        'data' => $data
    ];
    // 返回api接口
    return Response::create($result, $type);
}
