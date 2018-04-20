<?php

namespace app\api\service;

use app\api\lib\exception\OmisException;
use app\api\model\ArrPlane;
use think\Db;
use app\api\model\Main as MainModel;

class Oracle_con
{
    /**
     * 从Omis中获取所有航班数据
     * @return array
     */
    public static function getAllFlightByOmis()
    {
        $conn = config('oracle.oracle_conn');
        $sql = "select flt_id,to_char(flt_date,'YYYY-MM-DD') as
              flt_date,ac_id,dep_apt,arr_apt,to_char(std,'YYYY-MM-DD HH24:MI:SS') as
              std,to_char(sta,'YYYY-MM-DD HH24:MI:SS') as
              sta,to_char(off_time,'YYYY-MM-DD HH24:MI:SS') as
              off_time,to_char(on_time,'YYYY-MM-DD HH24:MI:SS') as
              on_time,etd,to_char(eta,'YYYY-MM-DD HH24:MI:SS') as
              eta,ac_stop_arr,ac_type,memo,cancel_flag from
              today_gyjw_flt_information where ac_id is not null order by std asc";
        $allFlights = self::getFlightByOmis($conn, $sql);
        $rs = self::saveAndUpdateAllFlight($allFlights);
        return $rs;
    }

    /**
     * 从Omis中获取当前的进港航班数据
     * @return array
     */
    public static function getCurrentArrFlightByOmis()
    {
        $conn = config('oracle.oracle_conn');
        $sql = "select flt_id,to_char(flt_date,'YYYY-MM-DD') as
              flt_date,ac_id,dep_apt,arr_apt,to_char(std,'YYYY-MM-DD HH24:MI:SS') as
              std,to_char(sta,'YYYY-MM-DD HH24:MI:SS') as
              sta,to_char(off_time,'YYYY-MM-DD HH24:MI:SS') as
              off_time,to_char(on_time,'YYYY-MM-DD HH24:MI:SS') as
              on_time,etd,to_char(eta,'YYYY-MM-DD HH24:MI:SS') as
              eta,ac_stop_arr,ac_type,memo,cancel_flag from
              today_gyjw_flt_information where ARR_APT='KWE' AND ac_id is not null
              order by std asc";
        $arrFlights = self::getFlightByOmis($conn, $sql);
        $rs = self::saveAndUpdateArrFlightData($arrFlights);
        return $rs;
    }

    /**
     * 从Oracle数据库中获取航班数据信息
     * @return mixed $arrFlights 数组类型 航班信息数据
     * @param array $conn
     * @param string $sql
     * @return mixed
     * @throws OmisException
     */
    public static function getFlightByOmis($conn = [], $sql = '')
    {
        $result = Db::connect($conn, true)->query($sql);
        $Flights = array_iconv('gb2312', 'utf-8', $result);
//        $arrFlights=[];
        if (!$Flights) {
            throw new OmisException();
        }
        return $Flights;
    }

    //调用此方法，保存和更新进港航班在arr_plane表中
    public static function saveAndUpdateArrFlightData($arrFlights)
    {
        $rs = ArrPlane::saveAndUpdate($arrFlights);
        return $rs;
    }

    //调用此方法，保存和更新所有航班在main表中
    public static function saveAndUpdateAllFlight($allFlights)
    {
        $rs = MainModel::saveAndUpdateAll($allFlights);
        return $rs;
    }
}

?>