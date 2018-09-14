<?php

namespace app\api\model;

use app\api\model\Main as MainModel;
use think\Exception;
use think\Model;

class ArrPlane extends Model
{

    // 定义关联模型
    public function attr()
    {
        return $this->hasOne('Attr', 'f_id');
    }

    //保存和更新进港航班数据到arr_plane表中
    public static function saveAndUpdate($arrFlights)
    {
        $status = [
            'updateArrFlighs' => [],
            'saveArrFlighs' => []
        ];
        foreach ($arrFlights as $arrFlight) {
            $planeInfo = self::where('flt_date', '=', $arrFlight['flt_date'])
                ->where('flt_id', '=', $arrFlight['flt_id'])
                ->where('dep_apt', '=', $arrFlight['dep_apt'])
                ->where('cancel_flag', '=', $arrFlight['cancel_flag'])// 修正增加之前取消的航班
                ->find();
            if (!$planeInfo) {
                $ArrPlaneModel = new ArrPlane;
                $Attrs = new Attr;
                $Attrs->eta = $arrFlight['eta'];
                $Attrs->on_time = $arrFlight['on_time'];
                //TODO 查找航前航后
                $ac_statusTemp = MainModel::getOverFlight($arrFlight);
                $ac_status = $ac_statusTemp ? $ac_statusTemp : 2;
                $ArrPlaneModel->ac_status = $ac_status;
                if ($arrFlight['off_time'] && $arrFlight['ac_stop_arr']) {
                    //TODO 获取排序
                    $order = self::_order($arrFlight['ac_stop_arr']);
                    $Attrs->order = $order;
                }
                $ArrPlaneModel->attr = $Attrs;
                try {
                    //捕获异常
                    $ArrPlaneModel->together('attr')->save($arrFlight);//自动写入（属性表Attr）
                    array_push($status['saveArrFlighs'], $arrFlight);
                } catch (Exception $ex) {
                    //TODO: 可以记录日志
                }
            } else {
                $ac_statusTemp = MainModel::getOverFlight($arrFlight);
                $ac_status = $ac_statusTemp ? $ac_statusTemp : 2; // $planeInfo['ac_status']
                if ($planeInfo['ac_status'] === -1) {
                    $ac_status = $planeInfo['ac_status'];
                }
                $ArrPlaneModel = new ArrPlane;
                //$planeInfo = $planeInfo->toArray();
                $plane_id['id'] = $planeInfo['id'];
                $ArrPlaneModel = $ArrPlaneModel::get($plane_id['id']);
                $arrFlight = array_merge($arrFlight, $plane_id);
                $ArrPlaneModel->flt_date = $arrFlight['flt_date'];
                $ArrPlaneModel->ac_id = $arrFlight['ac_id'];
                $ArrPlaneModel->dep_apt = $arrFlight['dep_apt'];
                $ArrPlaneModel->arr_apt = $arrFlight['arr_apt'];
                $ArrPlaneModel->std = $arrFlight['std'];
                $ArrPlaneModel->sta = $arrFlight['sta'];
                $ArrPlaneModel->off_time = $arrFlight['off_time'];
                $ArrPlaneModel->on_time = $arrFlight['on_time'];
                $ArrPlaneModel->etd = $arrFlight['etd'];
                $ArrPlaneModel->eta = $arrFlight['eta'];
                $ArrPlaneModel->ac_stop_arr = $arrFlight['ac_stop_arr'];
                $ArrPlaneModel->ac_type = $arrFlight['ac_type'];
                $ArrPlaneModel->memo = $arrFlight['memo'];
                $ArrPlaneModel->cancel_flag = $arrFlight['cancel_flag'];
                $ArrPlaneModel->attr()->on_time = $arrFlight['on_time'];
                $ArrPlaneModel->attr()->eta = $arrFlight['eta'];
                $ArrPlaneModel->ac_status = $ac_status;
                if ($arrFlight['ac_stop_arr']) {
                    //TODO 获取排序 && $planeInfo['ac_status'] == 2
                    $order = self::_order($arrFlight['ac_stop_arr']);
                    $ArrPlaneModel->attr->order = $order;
                }
//                $ArrPlaneModel->attr->save(array_merge($arrFlight, $plane_id));//合并数组
                try {
                    //捕获异常
                    $rs = $ArrPlaneModel->together('attr')->save();//自动更新（属性表）
                    if ($rs) {
                        array_push($status['updateArrFlighs'], $arrFlight);
                    }
                } catch (Exception $ex) {
                    //TODO: 可以记录日志
                }
            }
            //TODO 查找航后飞机逻辑
        }
        return $status;
    }

    public static function saveNewFlight($newFlight, $flag = true)
    {
        if ($flag) {
            $planeInfo = self::where('flt_date', '=', $newFlight['flt_date'])
                ->where('ac_id', '=', $newFlight['ac_id'])
                ->where('flt_id', '=', $newFlight['flt_id'])
                ->find();
            if (!$planeInfo) {
//                $newFlight['ac_status'] = 3;
                $rs = null;
                $ArrPlaneModel = new ArrPlane;
                $Attrs = new Attr;
                $Attrs->eta = $newFlight['eta'];
                $Attrs->on_time = $newFlight['on_time'];
                if ($newFlight['off_time'] && $newFlight['ac_stop_arr']) {
                    //TODO 获取排序
                    $order = self::_order($newFlight['ac_stop_arr']);
                    $Attrs->order = $order;
                }
                $ArrPlaneModel->attr = $Attrs;
                try {
                    //捕获异常
                    $ArrPlaneModel->together('attr')->save($newFlight);//自动写入（属性表Attr）
                    $rs = '新增成功！';
                } catch (Exception $ex) {
                    //TODO: 可以记录日志
                }
            } else {
                $newFlight['ac_status'] = $newFlight['ac_status'] ? $newFlight['ac_status'] : $planeInfo['ac_status'];
                $rs = null;
                $ArrPlaneModel = new ArrPlane;
                $plane_id['id'] = $planeInfo['id'];
                $ArrPlaneModel = $ArrPlaneModel::get($plane_id['id']);
                $arrFlight = array_merge($newFlight, $plane_id);
                $ArrPlaneModel->flt_date = $arrFlight['flt_date'];
                $ArrPlaneModel->ac_id = $arrFlight['ac_id'];
                $ArrPlaneModel->dep_apt = $arrFlight['dep_apt'];
                $ArrPlaneModel->arr_apt = $arrFlight['arr_apt'];
                $ArrPlaneModel->std = $arrFlight['std'];
                $ArrPlaneModel->sta = $arrFlight['sta'];
                $ArrPlaneModel->off_time = $arrFlight['off_time'];
                $ArrPlaneModel->on_time = $arrFlight['on_time'];
                $ArrPlaneModel->etd = $arrFlight['etd'];
                $ArrPlaneModel->eta = $arrFlight['eta'];
                $ArrPlaneModel->ac_stop_arr = $arrFlight['ac_stop_arr'];
                $ArrPlaneModel->ac_type = $arrFlight['ac_type'];
                $ArrPlaneModel->memo = $arrFlight['memo'];
                $ArrPlaneModel->cancel_flag = $arrFlight['cancel_flag'];
                $ArrPlaneModel->attr()->on_time = $arrFlight['on_time'];
                $ArrPlaneModel->attr()->eta = $arrFlight['eta'];
                if ($newFlight['off_time'] && $newFlight['ac_stop_arr']) {
                    //TODO 获取排序
                    $order = self::_order($newFlight['ac_stop_arr']);
                    $ArrPlaneModel->attr->order = $order;
                }
                try {
                    //捕获异常
                    $ArrPlaneModel->together('attr')->save();//自动写入（属性表Attr）
                    $rs = '更新成功！';
                } catch (Exception $ex) {
                    //TODO: 可以记录日志
                }
            }
        } else {
            $flt_date = $newFlight['flt_date'];
//            $flt_date = date('Y-m-d', strtotime("$flt_date+1 day"));
            $planeInfo = self::where('flt_date', '=', $flt_date)
                ->where('ac_id', '=', $newFlight['ac_id'])
                ->where('flt_id', '=', $newFlight['flt_id'])
                ->find();
            if (!$planeInfo) {
                $rs = null;
                $ArrPlaneModel = new ArrPlane;
                $Attrs = new Attr;
                $Attrs->eta = $newFlight['eta'];
                $Attrs->on_time = $newFlight['on_time'];
                if ($newFlight['ac_stop_arr']) {
                    //TODO 获取排序
                    $order = self::_order($newFlight['ac_stop_arr']);
                    $Attrs->order = $order;
                }
                $ArrPlaneModel->attr = $Attrs;
                try {
                    //捕获异常
                    $ArrPlaneModel->together('attr')->save($newFlight);//自动写入（属性表Attr）
                    $rs = '新增成功！';
                } catch (Exception $ex) {
                    //TODO: 可以记录日志
                }
            } else {
                $rs = '数据库中已存在！';
            }
        }
        return $rs;
    }

    public static function _order($data)
    {
        $order = substr($data, 1, 1);
        return $order;
    }
}

?>