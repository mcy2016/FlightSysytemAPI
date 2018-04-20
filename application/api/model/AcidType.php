<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/1/15
 * Time: 20:43
 */

namespace app\api\model;


use think\Exception;
use think\Model;

class AcidType extends Model
{
    // 获取所有的机型机号数据
    public static function getAll()
    {
        $rs = self::select();
        return $rs;
    }

    //根据机号获取机型数据
    public static function getByAcId($acId)
    {
        $rs = self::where('ac_id', '=', $acId)
            ->find();
        return $rs;
    }

    public static function updateAcType($data)
    {
        $result = [];
        $ac = self::where('ac_id', '=', $data['ac_id'])
            ->find();
        if (!$ac) {
            $ac = new AcidType();
            $ac->ac_id = $data['ac_id'];
            $ac->ac_type = $data['ac_type'];
            try {
                //捕获异常
                $res = $ac->save();
                if ($res) {
                    $result['code'] = 0;
                    $result['msg'] = '新增成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '新增失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        } else {
            $ac->ac_type = $data['ac_type'];
            try {
                //捕获异常
                $res = $ac->save();
                if ($res) {
                    $result['code'] = 0;
                    $result['msg'] = '更新成功！';
                } else {
                    $result['code'] = -1;
                    $result['msg'] = '更新失败！';
                }
            } catch (Exception $ex) {
                //TODO: 可以记录日志
            }
        }
        return $result;
    }
}