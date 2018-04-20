<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/11/18
 * Time: 12:12
 */

namespace app\api\model;


use app\api\lib\exception\MainException;
use think\Exception;
use think\Model;
use app\api\model\HandAttr as HandAttrModel;
use app\api\model\Flight as FlightModel;

class Main extends Model
{
    //保存和更新所有航班到main表中
    public static function saveAndUpdateAll($allFlights)
    {
        $status = [
            'updateFlighs' => [],
            'saveFlighs' => []
        ];
        foreach ($allFlights as $FlightInfo) {
            $planeInfo = self::where('flt_date', '=', $FlightInfo['flt_date'])
                ->where('flt_id', '=', $FlightInfo['flt_id'])
                ->where('dep_apt', '=', $FlightInfo['dep_apt'])
                ->where('arr_apt', '=', $FlightInfo['arr_apt'])
                ->find();
            if (!$planeInfo) {
                array_push($status['saveFlighs'], $FlightInfo);
            } else {
                $planeInfo = $planeInfo->toArray();
                $plane_id['id'] = $planeInfo['id'];
                array_merge($FlightInfo, $plane_id);
                array_push($status['updateFlighs'], array_merge($FlightInfo, $plane_id));
            }
        }
        $saveAllFlights = self::saveMain($status['saveFlighs']);
        $updateAllFlights = self::updateMain($status['updateFlighs']);
        return $arrFlightStatus = [
            'saveFlights' => $saveAllFlights,
            'updateFlights' => $updateAllFlights
        ];
    }

    //新增
    public static function saveMain($planeInfo)
    {
        $MainModel = new Main;
        try {
            //捕获异常
            $saveArrFlightStatus = $MainModel->saveAll($planeInfo);
        } catch (Exception $ex) {
            //TODO: 可以记录日志
            throw $ex;
        }
        return $saveArrFlightStatus;
    }

    //更新
    public static function updateMain($planeInfo)
    {
        $MainModel = new Main;
        try {
            //捕获异常
            $updateArrFlights = $MainModel->saveAll($planeInfo);
        } catch (Exception $ex) {
            //TODO: 可以记录日志
            throw $ex;
        }
        return $updateArrFlights;
    }

//    根据机号查询航班信息
    public static function getFlightByAcId($ac_id, $d, $flag = false)
    {
        if (!$ac_id) {
            throw new MainException(['msg' => '机号必须填']);
        }
        if (!$d) {
            $d = date('Y-m-d');
        }
        if ($flag) {
            // 查询航前飞机
            $flightInfo = self::where('ac_id', '=', $ac_id)
                ->where('flt_date', '=', $d)
                ->where('dep_apt', '=', 'KWE')
                ->find();
        } else {
            $flightInfo = self::where('ac_id', '=', $ac_id)
                ->where('flt_date', '=', $d)
                ->select();
        }
        //TODO 优化
//        if (!$flightInfo) {
//            throw new MainException(['msg' => $ac_id . '机号今日无航班']);
//        }
        return $flightInfo;
    }

//    根据机号查询航后飞机
    public static function getOverFlight($arrFlight)
    {
        $status = 1;//2过站，1航后，3航前
        if (!$arrFlight['ac_id']) {
            throw new MainException(['msg' => '缺少必要的机号']);
        }
        $overFlight = self::where('ac_id', '=', $arrFlight['ac_id'])
            ->where('flt_date', '=', $arrFlight['flt_date'])
            ->where('dep_apt', '=', 'KWE')
            ->where('std', '>', $arrFlight['std'])
            ->find();
        if (!$overFlight) {
            return $status = 1;//航后
        } else {
            return $status = null;
        }
    }

    public static function getNextTime($value, $data)
    {
        $nextTime=HandAttrModel::getByFId($data['id'],$data);
        if(!$nextTime){
            $nextTime = self::where('ac_id', '=', $data['ac_id'])
                ->where('std', '>', $data['sta'])
                ->where('dep_apt', '=', 'KWE')
                ->where('flt_date', '=', $data['flt_date'])
                ->find();
        }
        return $nextTime;
    }
}