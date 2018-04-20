<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/11/15
 * Time: 15:20
 */

namespace app\api\controller\v1;

use app\api\lib\utils\Param;
use app\api\model\Flight as FlightModel;
use think\Request;
use app\api\model\ArrPlane as ArrPlaneModel;
use app\api\model\HandAttr as HandAttrModel;
use app\api\model\AcidType as AcidTypeModel;

class Flight
{
    /**
     * 获取航班信息（联合属性表）接口
     * GET URL:/plane
     * @param string $d
     * @return \think\response\Json\
     */
    public function getArrFlight()
    {
        $request = Request::instance();
        $params = $request->param('date');
        $arrFlights = FlightModel::getArrFlightsByDate($params);
        return json($arrFlights);
    }

    public function getArrFlightMcc()
    {
        $request = Request::instance();
        $params = $request->param('date');
        $arrFlights = FlightModel::getArrFlightsMcc($params);
        return json($arrFlights);
    }

    public function getArrFlightAll()
    {
        $request = Request::instance();
        $params = $request->param('date');
        $arrFlights = FlightModel::getArrFlightsAllByDate($params);
        return json($arrFlights);
    }

    /*
     * 根据日期范围查询航班数据
     *
     */
    public function getArrFlightByDate()
    {
        $request = Request::instance();
        $params = $request->param();
        $arrFlights = FlightModel::getArrFlightDateRange($params['planeDate']);
//        return FlightModel::getLastSql();
        return json($arrFlights);
    }

    /**
     * 手动新增和更新航班接口
     * @return \think\response\Json
     */
    public function saveArrFlight()
    {
        $result = [];
        $request = Request::instance();
        $params = $request->param();
        $data = Param::newFlightParam($params);
        $rs = ArrPlaneModel::saveNewFlight($data);
        if (!$rs) {
            $result['code'] = -1;
            $result['msg'] = $rs;
        } else {
            $result['code'] = 0;
            $result['msg'] = $rs;
        }
        return json($result);
    }

    /**
     * 更新航班属性信息接口
     * @return \think\response\Json
     */
    public function updateArrFlight()
    {
        $request = Request::instance();
        $params = $request->param();
        //TODO  可以考虑使用一个方法处理传过来的参数
        $rs = FlightModel::updateArr($params);
        return json($rs);
    }

    public function updateDei()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = FlightModel::updateDei($params);
        return json($rs);
    }

    public function deleteArrFlight()
    {
        $result = [];
        $request = Request::instance();
        $params = $request->param();
        $rs = FlightModel::deleteArr($params);
        if (!$rs) {
            $result['msg'] = '删除航班信息失败';
            $result['code'] = -1;
        } else {
            $result['msg'] = '删除成功';
            $result['code'] = 0;
        }
        return json($result);
    }

    public function recoverArrFlight()
    {
        $result = [];
        $request = Request::instance();
        $params = $request->param();
        $rs = FlightModel::recover($params);
        if (!$rs) {
            $result['msg'] = '恢复航班信息失败';
            $result['code'] = -1;
        } else {
            $result['msg'] = '恢复成功';
            $result['code'] = 0;
        }
        return json($result);
    }

    /*
     * 导入航前航班
     */
    public function newBeforeFlight()
    {
        $request = Request::instance();
        $params = $request->param('date');
//        $d = date('Y-m-d');
        $rs = FlightModel::getBefore($params);
        return json($rs);
    }

    /**
     * @url /close
     * @type GET
     * 根据日期获取已关闭航班
     * @param string $d 日期
     * @return \think\response\Json
     */
    public function getCloseFlight($d = '')
    {
        $closedFlights = FlightModel::getCloseFlightByDate($d);
        return json($closedFlights);
    }

    /**
     * 更新和新增下一段起飞时间
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function updateNextTime()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = HandAttrModel::updateNextTime($params);
        return json($rs);
    }


    public function updateAcType()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = AcidTypeModel::updateAcType($params);
        return json($rs);
    }

    public function updateFltId()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = FlightModel::updateFltId($params);
        return json($rs);
    }

    public function updateAcId()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = FlightModel::updateAcId($params);
        return json($rs);
    }

    public function updateAcStopArr()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = FlightModel::updateAcStopArr($params);
        return json($rs);
    }

    public function updateOffTime()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = FlightModel::updateOffTime($params);
        return json($rs);
    }

    public function updateAcStatus()
    {
        $request = Request::instance();
        $params = $request->param();
        $rs = HandAttrModel::updateStatus($params);
        return json($rs);
    }
}