<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/2/9
 * Time: 11:25
 */

namespace app\api\controller\v1;


use think\Request;
use app\api\model\UserLog as UserLogModel;

class UserLog
{
    public function updateDutyUser()
    {
        $request = Request::instance();
        $cardIdParam = $request->param();
        $rs = UserLogModel::updateUserLog($cardIdParam, false);
//        if (!$rs) {
//            $result['code'] = -1;
//            $result['msg'] = '更新失败';
//        } else {
//            $result['code'] = 1;
//            $result['msg'] = '更新成功';
//        }
        return json($rs);
    }

    public function shiftByProfession()
    {
        $request = Request::instance();
        $dataRange = $request->param();
        $rs = UserLogModel::shiftByProfession($dataRange['date']);
        return json($rs);
    }

}