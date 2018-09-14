<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/11/15
 * Time: 17:30
 */

namespace app\api\model;

use app\api\lib\exception\FlightAttrException;
use app\api\lib\exception\FlightException;
use app\api\lib\utils\Param;
use Exception;
use think\Model;
use app\api\model\Route as RouteModel;
use app\api\model\Attr as AttrModel;
use app\api\model\Main as MainModel;
use app\api\model\ArrPlane as ArrPlaneModel;
use app\api\model\User as UserModel;
use app\api\model\AcidType as AcidTypeModel;

class Flight extends Model
{
    protected $table = 'f_arr_plane';

    // 定义关联模型
    public function attr()
    {
        return $this->hasOne('Attr', 'f_id');
    }

    // 获取器，根据机号获取机型
    public function getAcTypeAttr($value, $data)
    {
        $ac_type = AcidTypeModel::getByAcId($data['ac_id']);
        if (!$ac_type) {
            return '无';
        }
        return $ac_type;
    }

    public function getReceiveId1Attr($value, $data)
    {
        if ($value) {
            $receiveId = UserModel::getUserById($value);
            return $receiveId;
        }
        return $value;
    }

    //定义获取器
    public function getArrAptAttr($value, $data)
    {
        if ($value === 'KWE') {
            $status = ['KWE' => '贵阳'];
            return $status[$data['arr_apt']];
        } else {
            return $value;
        }
    }

    //定义获取器
    public function getDepAptAttr($value, $data)
    {
        $depAptAndFlightTime = RouteModel::getDepAptZh($value, $data);
        return $depAptAndFlightTime;
    }

    //定义获取器
    public function getStdAttr($value, $data)
    {
        $nextFlight = MainModel::getNextTime($value, $data);
        if (!$nextFlight) {
            $nextFlight['std'] = null;
        }
        return $nextFlight;
    }

    /**
     * @param $d 日期 "2017-11-15"
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getArrFlightsByDate($d)
    {
        $arrFlights = [];
        $FlightModle = new Flight;
        if (!$d) {
            $d = date('Y-m-d');
        }
        //关联查询
        $with['attr'] = function ($query) {
            $query->order('order', 'desc');
        };
        $arrOverFlights = self::where('flt_date', '=', $d)
            ->where('ac_status', '>', 0)
            ->where('off_time', '<>', '')
            ->with(['attr'])->order(['ac_stop_arr' => 'desc', 'ac_status' => 'desc', 'off_time' => 'asc', 'std' => 'asc'])->select();
        $arrFlight = self::where('flt_date', '=', $d)
            ->where('ac_status', '>', 0)
            ->where('off_time', '=', '')
            ->where('cancel_flag', '<>', 1)
            ->with(['attr'])->order(['std' => 'asc'])->select();
        $arrFlights = array_merge($arrOverFlights, $arrFlight);
        return $arrFlights;
    }

    public static function getArrFlightsAllByDate($d)
    {
        $arrFlights = [];
        $FlightModle = new Flight;
        if (!$d) {
            $d = date('Y-m-d');
        }
        //关联查询
        $with['attr'] = function ($query) {
            $query->order('order', 'desc');
        };
        $arrFlight = self::where('flt_date', '=', $d)
//            ->where('ac_status', '>', 0)
//            ->where('off_time', '=', '')
//            ->where('cancel_flag', '<>', 1)
            ->with(['attr'])->order(['std' => 'asc'])->select();
        return $arrFlight;
    }

    //Mcc 请求进港航班数据，和OMIS一样按照std排序
    public static function getArrFlightsMcc($d)
    {
        if (!$d) {
            $d = date('Y-m-d');
        }
        $arrFlights = self::where('flt_date', '=', $d)
            ->where('ac_status', '>', 0)
            ->where('cancel_flag', '<>', 1)
            ->with(['attr'])->order(['ac_status' => 'desc', 'std' => 'asc'])
            ->select();
        return $arrFlights;
    }

    //根据日期范围查询航班数据
    public static function getArrFlightDateRange($planeDate)
    {
        if (!$planeDate && !is_array($planeDate)) {
            return '';
        }
        $with['attr'] = function ($query) {
            $query->order('order', 'desc');
        };
        $arrFlight = self::where('cancel_flag', '<>', 1)
            ->where('flt_date', 'between', $planeDate)
            ->with(['attr'])->order(['std' => 'asc'])->select();
        return $arrFlight;
    }

    /**
     * 更新除冰信息
     * @param $data
     * @return array
     * @throws FlightAttrException
     */
    public static function updateDei($data)
    {
        $result = [];
        $flightAttr = AttrModel::getFlightAttr($data['f_id']);
        if (!$flightAttr) {
            throw new FlightAttrException();
        }
        $flightAttr['deicing_index'] = $data['deicing_index'];
        $flightAttr['deicing_sta'] = $data['deicing_sta'];
        $flightAttr['deicing_end'] = $data['deicing_end'];
        try {
            //捕获异常
            $rs = $flightAttr->save();
            if ($rs) {
                $result['code'] = 0;
                $result['msg'] = '更新成功';
            } else {
                $result['code'] = -1;
                $result['msg'] = '更新失败';
            }
        } catch (Exception $ex) {
            //TODO: 可以记录日志
        }
        return $result;
    }

    public static function updateFltId($data)
    {
        $result = [];
        $flight = self::where('id', '=', $data['f_id'])
            ->find();
        if ($flight) {
            // 更新
            $flight->flt_id = $data['flt_id'];
            try {
                //捕获异常
                $rs = $flight->save();
                if ($rs) {
                    $result['code'] = 0;
                    $result['msg'] = '航班号更新成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '航班号更新失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        } else {
            throw new FlightException(['msg' => '找不到该航班']);
        }
        return $result;
    }

    public static function updateAcId($data)
    {
        $result = [];
        $flight = self::where('id', '=', $data['f_id'])
            ->find();
        if ($flight) {
            $flight->ac_id = $data['ac_id'];
            try {
                //捕获异常
                $rs = $flight->save();
                if ($rs) {
                    $result['code'] = 0;
                    $result['msg'] = '机号更新成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '机号更新失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        } else {
            throw new FlightException(['msg' => '找不到该航班']);
        }
        return $result;
    }

    public static function updateAcStopArr($data)
    {
        $result = [];
        $flight = self::where('id', '=', $data['f_id'])
            ->find();
        if ($flight) {
            $flight->ac_stop_arr = $data['ac_stop_arr'];
            try {
                //捕获异常
                $rs = $flight->save();
                if ($rs) {
                    $result['code'] = 0;
                    $result['msg'] = '机位更新成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '机位更新失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        } else {
            throw new FlightException(['msg' => '找不到该航班']);
        }
        return $result;
    }

    public static function updateOffTime($data)
    {
        $result = [];
        $flight = self::where('id', '=', $data['f_id'])
            ->find();
        if ($flight) {
            $flight->off_time = $data['off_time'];
            try {
                //捕获异常
                $rs = $flight->save();
                if ($rs) {
                    $result['code'] = 0;
                    $result['msg'] = '实起时间更新成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '实起时间更新失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        } else {
            throw new FlightException(['msg' => '找不到该航班']);
        }
        return $result;
    }

    // 更新下一航段起飞时间后把此航班设置成航后
    public static function updateNextTime($data)
    {
        $flight = self::where('id', '=', $data['f_id'])->find();
        $flight->ac_status = 1;// 航后
        try {
            //捕获异常
            $rs = $flight->save();
        } catch (Exception $ex) {
            //TODO: 可以记录日志
        }
        if (!$rs) {
            throw new FlightException(['msg' => '更新成航后失败']);
        }
        return $rs;
    }

//    public static function updateStatus($data)
//    {
//        $result = [];
//        $flight = self::where('id', '=', $data['f_id'])->find();
//        $flight->ac_status = $data['ac_status'];
//        try {
//            //捕获异常
//            $rs = $flight->save();
//            if ($rs) {
//                $result['code'] = 0;
//                $result['msg'] = '航班状态更新成功！';
//            } else {
//                $result['code'] = -1;
//                $result['msg'] = '航班状态更新失败！';
//            }
//        } catch (Exception $ex) {
//            //TODO: 可以记录日志
//        }
//        if (!$rs) {
//            throw new FlightException(['msg' => '更新航班状态失败']);
//        }
//        return $result;
//    }

    /**
     * 更新航班属性表
     * @param array $data
     */
    public static function updateArr($data)
    {
        $attr = [];
        if (!array_key_exists('f_id', $data)) {
            throw new FlightAttrException(['msg' => '必须传入参数f_id']);
        }
        $flightAttr = AttrModel::getFlightAttr($data['f_id']);
        //更新
        if (!$flightAttr) {
            throw new FlightAttrException();
        }
        $flightAttr['compute_time'] = array_key_exists('compute_time', $data) ? $data['compute_time'] : $flightAttr['compute_time'];
//            $flightAttr['compute_time'] = $flightAttr['compute_time'] ? $flightAttr['compute_time'] : array_key_exists('compute_time', $data) ? $data['compute_time'] : null;
        $flightAttr['receive_id1'] = array_key_exists('receive_id1', $data) ? $data['receive_id1'] : $flightAttr['receive_id1'];
//            $flightAttr['receive_id1'] = $flightAttr['receive_id1'] ? $flightAttr['receive_id1'] : array_key_exists('receive_id1', $data) ? $data['receive_id1'] : null;
//        $flightAttr['receive_id2'] = $flightAttr['receive_id2'] ? $flightAttr['receive_id2'] : array_key_exists('receive_id2', $data) ? $data['receive_id2'] : null;
        $flightAttr['receive_id2'] = array_key_exists('receive_id2', $data) ? $data['receive_id2'] : $flightAttr['receive_id2'];
//            $flightAttr['send_id1'] = $flightAttr['send_id1'] ? $flightAttr['send_id1'] : array_key_exists('send_id1', $data) ? $data['send_id1'] : null;
        $flightAttr['send_id1'] = array_key_exists('send_id1', $data) ? $data['send_id1'] : $flightAttr['send_id1'];
//            $flightAttr['send_id2'] = $flightAttr['send_id2'] ? $flightAttr['send_id2'] : array_key_exists('send_id2', $data) ? $data['send_id2'] : null;
        $flightAttr['send_id2'] = array_key_exists('send_id2', $data) ? $data['send_id2'] : $flightAttr['send_id2'];
        $flightAttr['round_id'] = array_key_exists('round_id', $data) ? $data['round_id'] : $flightAttr['round_id'];
//        $flightAttr['round_id'] = $flightAttr['round_id'] ? $flightAttr['round_id'] : array_key_exists('round_id', $data) ? $data['round_id'] : null;
//        $flightAttr['release_id'] = $flightAttr['release_id'] ? $flightAttr['release_id'] : array_key_exists('release_id', $data) ? $data['release_id'] : null;
        $flightAttr['release_id'] = array_key_exists('release_id', $data) ? $data['release_id'] : $flightAttr['release_id'];
        $flightAttr['remark'] = array_key_exists('remark', $data) ? $data['remark'] : $flightAttr['remark'];
        $flightAttr['reminder_time'] = array_key_exists('reminder_time', $data) ? $data['reminder_time'] : $flightAttr['reminder_time'];

//        $flightAttr['remark'] = $flightAttr['remark'] ? $flightAttr['remark'] : array_key_exists('remark', $data) ? $data['remark'] : '';
//        $flightAttr['reminder_time'] = $flightAttr['reminder_time'] ? $flightAttr['reminder_time'] : array_key_exists('reminder_time', $data) ? $data['reminder_time'] : null;
        $flightAttr['ac_status'] = array_key_exists('ac_status', $data) ? $data['ac_status'] : 2;
        try {
            //捕获异常
            $rs = $flightAttr->save();
            if ($rs) {
//                array_push($attr['flightAttr'], $flightAttr);
                $attr['code'] = 0;
                $attr['msg'] = '更新' . $rs . '条数据';
                $attr['data'] = $flightAttr;
            } else {
                $attr['msg'] = '没有更新数据';
                $attr['code'] = -1;
                $attr['data'] = [];
            }
        } catch (Exception $ex) {
            //TODO: 可以记录日志
        }
        return $attr;
    }

    /**
     * 删除航班
     * @param $data
     * @return false|int
     * @throws FlightException
     */
    public static function deleteArr($data)
    {
        if (!array_key_exists('id', $data)) {
            throw new FlightException(['msg' => '参数错误，请传航班id']);
        }
        $flight = self::get($data['id']);
        if (!$flight) {
            throw new FlightException(['msg' => '删除的航班号不对嘛，都没有此id的航班']);
        }
        $flight->ac_status = -1;
        $rs = $flight->save();
        if (!$rs) {
            throw new FlightException(['msg' => '删除航班信息失败']);
        }
        return $rs;
    }

    /**
     * 恢复删除的航班
     */
    public static function recover($data)
    {
        if (!array_key_exists('id', $data)) {
            throw new FlightException(['msg' => '参数错误，请传航班id']);
        }
        $flight = self::get($data['id']);
        if (!$flight) {
            throw new FlightException(['msg' => '恢复的航班号不对嘛，都没有此id的航班']);
        }
        $flight->ac_status = $data['dep_apt']['dep_apt_zh'] === '贵阳' ? 3 : 2;
        $rs = $flight->save();
        if (!$rs) {
            throw new FlightException(['msg' => '恢复航班信息失败']);
        }
        return $rs;
    }

    //获取航前飞机
    public static function getBefore($d)
    {
        $result = [
            'data' => []
        ];
        $res = [];
        $yestoday = date('Y-m-d', strtotime("$d-1 day"));//获取前一天时间
        //$d = date("Y-m-d", strtotime("$d-1 day"));
        $flightBefore = self::where('flt_date', '=', $yestoday)
            ->where('ac_status', '=', '1')
            ->select();
        foreach ($flightBefore as $item) {
            $todayBeforeFlight = MainModel::getFlightByAcId($item['ac_id'], $d, true);
            if (!$todayBeforeFlight) {
                $item['flt_id'] = $item['flt_id'] . '机号异常';
                $item['flt_date'] = $d;
                $item['dep_apt'] = 'KWE';
                $todayBeforeFlight = $item;
            }
            $todayBeforeFlight = $todayBeforeFlight->data;
            $data = Param::newFlightParam($todayBeforeFlight);
            $data['ac_status'] = 3;//航前
            $data['off_time'] = $data['std'];
            $data['ac_stop_arr'] = $item['ac_stop_arr'];
//            if (!$data['ac_stop_arr']) {
//                $data['ac_stop_arr'] = $item['ac_stop_arr'];
//            }
            $rs = ArrPlaneModel::saveNewFlight($data, false);
            if (!$rs) {
                $res['code'] = -1;
                $res['msg'] = '新增失败';
                $res['data'] = [];
                array_push($result['data'], $res);
            } else {
                $res['code'] = 0;
                $res['msg'] = $rs;
                $res['data'] = $data;
                array_push($result['data'], $res);
            }
        }
        return $result;
    }

    //根据日期 获取已关闭航班，ac_status===1
    public static function getCloseFlightByDate($d)
    {
        $FlightModle = new Flight;
        if (!$d) {
            $d = date('Y-m-d');
        }
        //关联查询
        $closeFlights = $FlightModle->where('flt_date', '=', $d)
            ->where('ac_status', '=', -1)
            ->with('attr')
            ->select();
        return $closeFlights;
    }
}