<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/12
 * Time: 11:21
 */

namespace app\api\lib\utils;


class Param
{
    public static function paramUtil($data)
    {
        // $data=$data->toArray();
        $a = $data['flt_id'];
        return $a;
    }

    public static function newFlightParam($newFlightData)
    {
        $newFlight = [];
        $newFlight['flt_id'] = array_key_exists('flt_id', $newFlightData) ? $newFlightData['flt_id'] : null;
        $newFlight['flt_date'] = array_key_exists('flt_date', $newFlightData) ? $newFlightData['flt_date'] : date('Y-m-d');
        $newFlight['ac_id'] = array_key_exists('ac_id', $newFlightData) ? $newFlightData['ac_id'] : null;
        $newFlight['dep_apt'] = array_key_exists('dep_apt', $newFlightData) ? $newFlightData['dep_apt'] : null;
        $newFlight['arr_apt'] = array_key_exists('arr_apt', $newFlightData) ? $newFlightData['arr_apt'] : 'KWE';
        $newFlight['std'] = array_key_exists('std', $newFlightData) ? $newFlightData['std'] : null;
        $newFlight['sta'] = array_key_exists('sta', $newFlightData) ? $newFlightData['sta'] : null;
        $newFlight['off_time'] = array_key_exists('off_time', $newFlightData) ? $newFlightData['off_time'] : null;
        $newFlight['on_time'] = array_key_exists('on_time', $newFlightData) ? $newFlightData['on_time'] : null;
        $newFlight['etd'] = array_key_exists('etd', $newFlightData) ? $newFlightData['etd'] : null;
        $newFlight['eta'] = array_key_exists('eta', $newFlightData) ? $newFlightData['eta'] : null;
        $newFlight['ac_stop_arr'] = array_key_exists('ac_stop_arr', $newFlightData) ? $newFlightData['ac_stop_arr'] : null;
        $newFlight['ac_type'] = array_key_exists('ac_type', $newFlightData) ? $newFlightData['ac_type'] : null;
        $newFlight['memo'] = array_key_exists('memo', $newFlightData) ? $newFlightData['memo'] : null;
        $newFlight['remarks'] = array_key_exists('remark', $newFlightData) ? $newFlightData['remark'] : null;
        $newFlight['ac_status'] = array_key_exists('ac_status', $newFlightData) ? $newFlightData['ac_status'] : null;
        return $newFlight;
    }

    private function _formatDate()
    {
        $nowDate = date('Y-m-d H:i:s');
        return $nowDate;
    }
}