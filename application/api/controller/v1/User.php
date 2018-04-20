<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/13
 * Time: 14:03
 */

namespace app\api\controller\v1;

use app\api\lib\exception\UserException;
use app\api\model\UserLog;
use think\Controller;
use app\api\model\User as UserModel;
use app\api\model\UserLog as UserLogModel;
use think\Request;

class User extends Controller
{

    /**
     * 根据用户id查找用户信息
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getById($id)
    {
        $user = UserModel::get($id);
        return json($user);
    }

    public function getAllUser()
    {
        $user = UserModel::getDutyUser($status = 0, $condition = '>=');
        return json($user);
    }

    /**
     * @type GET
     * 获取值班人员接口
     * @return \think\response\Json
     */
    public function getDutyUser()
    {
        $dutyUser = UserLogModel::getDutyUsers($status = 1, $condition = '=');
        return json($dutyUser);
    }

    /**
     * 打卡接口
     * @type POST
     * @param $cartId
     * @return bool
     * @throws \app\api\lib\exception\UserException
     */
    public function cardUser()
    {
        $result = [];
        $request = Request::instance();
        $cardIdParam = $request->param('card_id');
//        $rs = UserModel::updateUserStatus($cartIdParam, 2);
        $rs = UserLogModel::userPunchCard($cardIdParam);
        if (!$rs) {
            $result['code'] = -1;
            $result['msg'] = '打卡不成功！';
        } else {
            $result['code'] = 0;
            $result['msg'] = '打卡成功！';
        }
        return json($result);
    }

    /**
     * 手动打卡 可以多人一起
     * @return \think\response\Json
     * @throws \app\api\lib\exception\UserException
     */
    public function handCardUser()
    {
        $result = [];
        $request = Request::instance();
        $param = $request->param();
        $rs = UserLogModel::updateUserStatus($param['card_id'], 1);
        $result['code'] = $rs['code'];
        $result['data'] = $rs['data'];
        if (!$rs['data']) {
            $result['msg'] = '打卡不成功！';
        } else {
            $result['msg'] = '打卡成功！';
        }
        return json($result);
    }

    /**
     * 移除用户值班
     * @return \think\response\Json
     * @throws UserException
     */
    public function deleteDuty()
    {
        $result = [];
        $request = Request::instance();
        $param = $request->param();
        $rs = UserLog::updateUserStatus($param['id'], 0);
        $result['code'] = $rs['code'];
        $result['data'] = $rs['data'];
        if (!$rs['data']) {
            $result['code'] = -1;
            $result['msg'] = '失败！';
        } else {
            $result['code'] = 1;
            $result['msg'] = '成功！';
        }
        return json($result);
    }

    /**
     * 交班逻辑
     * type POST
     * @return \think\response\Json
     */
    public function nextDuty()
    {
        $result = [
            'data' => [],
            'pre' => [],
            'code' => '',
            'msg' => ''
        ];
        $rs = UserLogModel::nextShift();
        if ($rs['code'] = 1) {
            $result['data'] = $rs['data'];
            $result['pre'] = $rs['pre'];
            $result['code'] = 1;
            $result['msg'] = '交班成功！';
        } else {
            $result['code'] = 0;
            $result['msg'] = '交班失败！';
        }
        return json($result);
    }

    public function updateUser()
    {
        $result = [];
        $request = Request::instance();
        $cardIdParam = $request->param();
        if (array_key_exists('user_id', $cardIdParam)) {
            $user = UserModel::getUserByName($cardIdParam['username']);
            if (!$user) {
                throw new UserException();
            }
            $rs = UserModel::updateUser($cardIdParam);
            if (!$rs) {
                $result['code'] = -1;
                $result['msg'] = '更新失败';
//                return json(['code' => -1, 'msg' => '更新失败']);
            } else {
//              更新成功后，重新更新user_log表里的数据，调用UserLogModel里的更新打卡方法
                $rs = UserLogModel::updateUserLog($cardIdParam, true);
                if ($rs['code'] === 1) {
                    $result['code'] = 1;
                    $result['msg'] = '更新成功';
                }
            }
        } else {
            $rs = UserModel::newUser($cardIdParam);
            if (!$rs) {
                $result['code'] = -1;
                $result['msg'] = '新增失败';
            } else {
                $result['code'] = 1;
                $result['msg'] = '新增成功';
            }
        }
        return json($result);
    }
}