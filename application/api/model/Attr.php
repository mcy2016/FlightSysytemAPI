<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/11/14
 * Time: 14:46
 */

namespace app\api\model;


use app\api\lib\exception\AttrException;
use app\api\lib\exception\FlightAttrException;
use think\Exception;
use think\Model;
use app\api\model\User as UserModel;

class Attr extends Model
{
    public function getReceiveId1Attr($value, $data)
    {
        if ($value) {
            $receiveId = UserModel::getUserById($value);
            return $receiveId;
        }
        return $value;
    }

    public function getReceiveId2Attr($value, $data)
    {
        if ($value) {
            $receiveId = UserModel::getUserById($value);
            return $receiveId;
        }
        return $value;
    }

    public function getSendId1Attr($value, $data)
    {
        if ($value) {
            $receiveId = UserModel::getUserById($value);
            return $receiveId;
        }
        return $value;
    }

    public function getSendId2Attr($value, $data)
    {
        if ($value) {
            $receiveId = UserModel::getUserById($value);
            return $receiveId;
        }
        return $value;
    }

    public function getRoundIdAttr($value, $data)
    {
        if ($value) {
            $receiveId = UserModel::getUserById($value);
            return $receiveId;
        }
        return $value;
    }

    public function getReleaseIdAttr($value, $data)
    {
        if ($value) {
            $receiveId = UserModel::getUserById($value);
            return $receiveId;
        }
        return $value;
    }

    //新增或者更新航班属性数据
    public static function saveAndUpdateAttr($flightAttrs)
    {
        try {
            //捕获异常
            foreach ($flightAttrs as $flightAttr) {
                $attrInfo = self::where('f_id', '=', $flightAttr['id'])->find();
                if (!$attrInfo) {
                    //新增
                    $attr['f_id'] = $flightAttr['id'];
                    $attr['eta'] = $flightAttr['eta'];
                    $attr['on_time'] = $flightAttr['on_time'];
                    $AttrModel = new Attr();
                    $AttrModel->save($attr);
                } else {
                    //更新
                    $attr['f_id'] = $flightAttr['id'];
                    $attr['eta'] = $flightAttr['eta'];
                    $attr['on_time'] = $flightAttr['on_time'];
                    $updateFlightNum = self::get($attrInfo['id'])->save($attr);
                }
            }
        } catch (Exception $ex) {
            //TODO: 可以记录日志
            throw $ex;
        }

        return true;
    }

//    根据f_id查找航班相关属性信息
    public static function getFlightAttr($f_id)
    {
        $flightAttr = self::where('f_id', '=', $f_id)->find();
        return $flightAttr;
    }


}