<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/1/11
 * Time: 10:59
 */

namespace app\api\model;


use app\api\lib\exception\UserException;
use Exception;
use think\Model;
use app\api\model\User as UserModel;

class UserLog extends Model
{
    protected $autoWriteTimestamp = 'datetime';   // datetime 类型

    public static function getDutyUsers($status, $condition)
    {
        $dutyUsers = self::where('status', $condition, $status)
            ->order('convert(username using gb2312) asc')
            ->select();
        return $dutyUsers;
    }

    // 打卡业务
    public static function userPunchCard($cardId)
    {
        $today = date('Y-m-d');
        $user = UserModel::getUserByCard($cardId);
        if (!$user) {
            throw new UserException(['msg' => '用户卡号不存在', 'errorCode' => 40004]);
        }
        $UserLog = self::where('card_id', '=', $cardId)
            ->where('shift_date', '=', $today)
            ->find();
        if ($UserLog['status'] === 2) {
            throw new UserException(['msg' => '已打卡', 'errorCode' => 40002]);
        }
        if ($UserLog['status'] === 1) {
            throw new UserException(['msg' => '值班中', 'errorCode' => 40003]);
        }
        // 插入user_log表
        $UserLog = new userLog();
        $UserLog->username = $user['username'];
        $UserLog->rank = $user['rank'];
        $UserLog->status = 2;
        $UserLog->scope = $user['scope'];
        $UserLog->card_id = $user['card_id'];
        $UserLog->user_id = $user['id'];
        $UserLog->phone = $user['phone'];
        $UserLog->duty = $user['duty'];
        $UserLog->shift = (date('H') + 0) < 14 ? 2 : 1;// 1为晚班，2为白班
        $UserLog->shift_date = $today;
        $cardStatus = $UserLog->save();
        return $cardStatus;
    }

    // 交班业务
    public static function nextShift()
    {
        /* 1.查询正在值班的人员；
         2.根据现在的时间状态查询的是白班还是晚班，小于14点查询白班，否则查询晚班；
         3.把正在值值白班的人员状态更改为0；
         4.把正在值班人员的状态更改为0，把打卡的人员的状态更改为1；*/
        $result = [
            'data' => [],
            'pre' => [],
            'code' => 1
        ];
        $today = date('H');
        if ($today < 14) {
            $nowduty = self::where('status', '=', 1)
                ->where('shift', '=', 1)
                ->select();
            if (!$nowduty) {
                $result['pre'] = [];
            } else {
                foreach ($nowduty as $item) {
                    $item->status = 0;
                    $rs = $item->save();
                    array_push($result['pre'], $item);
                    $result['code'] = 1;
                }
            }
            $nextduty = self::where('status', '=', 2)
                ->where('shift', '=', 2)
                ->select();
            foreach ($nextduty as $item) {
                $item->status = 1;
                $rs = $item->save();
                array_push($result['data'], $item);
                $result['code'] = 1;
            }
        } else {
            $duty = self::where('status', '=', 1)
                ->where('shift', '=', 2)
                ->select();
            if (!$duty) {
                $result['pre'] = [];
            } else {
                foreach ($duty as $item) {
                    $item->status = 0;
                    $rs = $item->save();
                    array_push($result['pre'], $item);
                    $result['code'] = 1;
                }
            }
            $nextduty = self::where('status', '=', 2)
                ->where('shift', '=', 1)
                ->select();
            foreach ($nextduty as $item) {
                $item->status = 1;
                $rs = $item->save();
                array_push($result['data'], $item);
                $result['code'] = 1;
            }
        }
        return $result;
    }

    public static function updateUserStatus($cardIdArr, $status)
    {
        $today = date('Y-m-d');
        $userStatus = [
            'data' => [],
            'code' => ''
        ];
        if (is_array($cardIdArr)) {
            foreach ($cardIdArr as $item) {
                $user = UserModel::getUserByCard($item['card_id']);
                if (!$user) {
                    throw new UserException(['msg' => '没有此用户']);
                }
                $UserLog = self::where('user_id', '=', $item['id'])
                    ->where('shift_date', '=', $today)
                    ->find();
                if ($UserLog) {
                    $UserLog->status = $status;
                } else {
                    $user = $user->toArray();
                    $UserLog = new userLog();
                    $UserLog->username = $user['username'];
                    $UserLog->rank = $user['rank'];
                    $UserLog->status = $status;
                    $UserLog->scope = $user['scope'];
                    $UserLog->card_id = $user['card_id'];
                    $UserLog->user_id = $user['id'];
                    $UserLog->shift = (date('H') + 0) < 14 ? 2 : 1;
                    $UserLog->shift_date = $today;
//                    $cardStatus = $UserLog->save();
                }
                try {
                    //捕获异常
                    $rs = $UserLog->save();
                    array_push($userStatus['data'], $UserLog->toArray());
                    $userStatus['code'] = 1;
                } catch (Exception $ex) {
                    //TODO: 可以记录日志
                }
            }
        } else {
            $rs = self::_deleteDuty($cardIdArr, $status);
            if ($rs) {
                $userStatus['code'] = 1;
                $userStatus['data'] = $cardIdArr;
                $userStatus['msg'] = '移除成功！';
            } else {
                $userStatus['code'] = -1;
                $userStatus['data'] = '';
                $userStatus['msg'] = '移除失败！';
            }
        }
        return $userStatus;
    }

    // 移除值班人员
    private static function _deleteDuty($id, $status)
    {
        $user = self::where('id', '=', $id)
            ->find();
        $user->status = $status;
        try {
            //捕获异常
            $rs = $user->save();
        } catch (Exception $ex) {
            //TODO: 可以记录日志
        }
        return $rs;
    }

    // 更新值班日志里的用户属性
    public static function updateUserLog($userLogAttr, $flag)
    {
        $userStatus = [
            'data' => [],
            'code' => '',
            'msg' => ''
        ];
        if (!array_key_exists('userlog_id', $userLogAttr)) {
            $userLogExceptId = self::where('user_id', '=', $userLogAttr['user_id'])
                ->order('create_time', 'desc')
                ->limit(1)
                ->find();
            $userLogAttr['userlog_id'] = $userLogExceptId['id'];
//            $userLogAttr['status'] = // $userLogExceptId['status'];
        }
        $userLog = self::where('id', '=', $userLogAttr['userlog_id'])
            ->find();
        if ($userLog) {
            $userLog->username = $userLogAttr['username'];
            $userLog->rank = $userLogAttr['rank'];
//            $userLog->scope = $userLogAttr['scope'];
            $userLog->status = $userLogAttr['status'];
            $userLog->card_id = $userLogAttr['card_id'];
            $userLog->phone = $userLogAttr['phone'];
            $userLog->profession = $userLogAttr['profession'];
            try {
                //捕获异常
                $rs = $userLog->save();
                if ($rs) {
                    if ($flag) {
                        // 来自更新user表的请求，在此前更新过user表了，不用再更新，直接返回结果
                        $userStatus['code'] = 1;
                        $userStatus['msg'] = '更新成功！';
//                        $userData['data'] = $rs;
                    } else {
                        $res = UserModel::updateUser($userLogAttr);
                        if ($res) {
                            $userStatus['code'] = 1;
                            $userStatus['msg'] = '更新成功！';
                            array_push($userStatus['data'], $userLog->toArray());
                        } else {
                            $userStatus['code'] = -1;
                            $userStatus['msg'] = '只更新了值班日志表！';
                        }
                    }
                } else {
                    $userStatus['code'] = -1;
                    $userStatus['msg'] = '没有数据更新';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        } else {
            $userStatus['code'] = 1;
            $userStatus['data'] = '';
            $userStatus['msg'] = '更新成功，日志表无数据！';
        }
        return $userStatus;
    }

    // 查询时间范围内白班和晚班各专业出勤人次数
    public static function shiftByProfession($dataRange)
    {
//        查询白班各专业出勤人数
        $shiftProfession = [];
//        $dataRange = ['2018-04-13', '2018-04-13'];
//        $dayShiftProfession = self::_dayShiftProfession($dataRange);
//        $nightShiftProfession = self::_nightShiftProfession($dataRange);
        $shiftProfession = self::shiftProfession($dataRange);
        return $shiftProfession;
    }

    // 查询各专业人员出勤
    public static function shiftProfession($dataRange)
    {
        $shiftProfession = [];
//        $dataRange = ['2018-04-13', '2018-04-13'];
        $avCount = self::where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'AV')
            ->select();
        $meCount = self::where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'ME')
            ->select();
        $svCount = self::where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'SV')
            ->select();
        $xjCount = self::where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'XJ')
            ->select();
        $shiftProfession['AV'] = $avCount;
        $shiftProfession['ME'] = $meCount;
        $shiftProfession['SV'] = $svCount;
        $shiftProfession['XJ'] = $xjCount;
        return $shiftProfession;
    }

    public static function _dayShiftProfession($dataRange)
    {
        $dayShiftProfession = [];
//        $dataRange = ['2018-04-13', '2018-04-13'];
        $avCount = self::where('shift', '=', 2)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'AV')
            ->select();
        $meCount = self::where('shift', '=', 2)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'ME')
            ->select();
        $svCount = self::where('shift', '=', 2)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'SV')
            ->select();
        $xjCount = self::where('shift', '=', 2)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'XJ')
            ->select();
        $dayShiftProfession['AV'] = $avCount;
        $dayShiftProfession['ME'] = $meCount;
        $dayShiftProfession['SV'] = $svCount;
        $dayShiftProfession['XJ'] = $xjCount;
        return $dayShiftProfession;
    }

    public static function _nightShiftProfession($dataRange)
    {
        $nightShiftProfession = [];
//        $dataRange = ['2018-04-13', '2018-04-13'];
        $avCount = self::where('shift', '=', 1)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'AV')
            ->select();
        $meCount = self::where('shift', '=', 1)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'ME')
            ->select();
        $svCount = self::where('shift', '=', 1)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'SV')
            ->select();
        $xjCount = self::where('shift', '=', 1)
            ->where('shift_date', 'between', $dataRange)
            ->where('profession', '=', 'XJ')
            ->select();
        $nightShiftProfession['AV'] = $avCount;
        $nightShiftProfession['ME'] = $meCount;
        $nightShiftProfession['SV'] = $svCount;
        $nightShiftProfession['XJ'] = $xjCount;
        return $nightShiftProfession;
    }
}