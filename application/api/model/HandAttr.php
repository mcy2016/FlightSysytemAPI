<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/1/24
 * Time: 17:17
 */

namespace app\api\model;


use app\api\lib\exception\FlightException;
use think\Exception;
use think\Model;
use app\api\model\Flight as FlightModel;

class HandAttr extends Model
{
    /**
     * 更新和新增实起及下一航段起飞时间
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function updateNextTime($data)
    {
        $result = [];
        $flight = self::where('f_id', '=', $data['f_id'])
            ->where('flt_date', '=', $data['flt_date'])
            ->find();
        if (!$flight) {
            //新增一条记录
            $flight = new HandAttr();
            $flight->f_id = $data['f_id'];
            $flight->flt_date = $data['flt_date'];
            $flight->next_time = $data['next_time'];
            $flight->on_time = array_key_exists('on_time', $data) ? $data['on_time'] : null;
            $flight->status = 1;

            try {
                //捕获异常
                $rs = $flight->save();
                if (!$rs) {
                    $result['code'] = -1;
                    $result['msg'] = '出港时间无更新';
                } else {
                    $result['code'] = 0;
                    $result['msg'] = '出港时间更新成功';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        } else {
            // 更新下一航段起飞时间后把此航班设置成航后
            if (!$data['next_time']) {
//                $res = FlightModel::updateNextTime($data);
                $flight->ac_status = 1; // 航后
                $flight->status = 1;

            }
            $flight->next_time = $data['next_time'];
            $flight->on_time = array_key_exists('on_time', $data) ? $data['on_time'] : null;
            try {
                //捕获异常
                $rs = $flight->save();
                if ($rs) {
                    $result['code'] = 0;
                    $result['msg'] = '成功';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '失败';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        }
        return $result;
    }

    public static function updateStatus($data)
    {
        $result = [];
        $flight = self::where('f_id', '=', $data['f_id'])
            ->where('flt_date', '=', $data['flt_date'])
            ->find();
        if (!$flight) {
            $flight = new HandAttr();
            $flight->f_id = $data['f_id'];
            $flight->flt_date = $data['flt_date'];
            $flight->ac_status = $data['ac_status'];
            $flight->status = 1;
            if ($data['ac_status'] === 1) {
                // 改成航后
                $flight->next_time = '航后';
            }
            try {
                //捕获异常
                $rs = $flight->save();
                if ($rs) {
                    $result['code'] = 0;
                    $result['msg'] = '航班状态更新成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '航班状态更新失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
            if (!$rs) {
                throw new FlightException(['msg' => '更新航班状态失败']);
            }
        } else {
            if ($data['ac_status'] === 1) {
                // 改成航后
                $flight->next_time = '航后';
            }
            $flight->ac_status = $data['ac_status'];
            $flight->status = 1;
            try {
                //捕获异常
                $rs = $flight->save();
                if ($rs) {
                    $result['code'] = 0;
                    $result['msg'] = '航班状态更新成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '航班状态更新失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
            if (!$rs) {
                throw new FlightException(['msg' => '更新航班状态失败']);
            }
        }
        return $result;
    }
//    public static function updateOffTime($data)
//    {
//        $result = [];
//        $flight = self::where('f_id', '=', $data['f_id'])
//            ->where('flt_date', '=', $data['flt_date'])
//            ->find();
//        if (!$flight) {
//            //新增一条记录
//            $flight = new HandAttr();
//            $flight->f_id = $data['f_id'];
//            $flight->flt_date = $data['flt_date'];
//            $flight->next_time = $data['next_time'];
//            $flight->off_time = $data['off_time'];
//
//            try {
//                //捕获异常
//                $rs = $flight->save();
//                if (!$rs) {
//                    $result['code'] = -1;
//                    $result['msg'] = '失败';
//                } else {
//                    $result['code'] = 0;
//                    $result['msg'] = '成功';
//                }
//            } catch (Exception $ex) {
//                //TODO: 可以记录日志
//            }
//        } else {
//            if (!$data['next_time']) {
//                $res = FlightModel::updateNextTime($data);
//            }
//            $flight->next_time = $data['next_time'];
//            try {
//                //捕获异常
//                $rs = $flight->save();
//                if ($rs) {
//                    $result['code'] = 0;
//                    $result['msg'] = '成功';
//                } else {
//                    $result['code'] = -1;
//                    $result['msg'] = '失败';
//                }
//            } catch (Exception $ex) {
//                //TODO: 可以记录日志
//            }
//        }
//        return $result;
//    }

    public static function getByFId($f_id, $data)
    {
        $nextTime = self::where('f_id', '=', $f_id)
            ->where('flt_date', '=', $data['flt_date'])
            ->find();
        return $nextTime;
    }
}