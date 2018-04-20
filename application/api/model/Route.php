<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/11/15
 * Time: 16:18
 */

namespace app\api\model;


use app\api\lib\exception\FlightRouteException;
use think\Db;
use think\Model;

class Route extends Model
{
//    protected $hidden = ['arr_apt', 'id', 'dep_apt'];

    public static function getDepAptZh($dep_apt, $data)
    {
        if ($dep_apt == null) {
            return '进港航站为空';
//            throw new FlightRouteException(['msg' => $data['flt_id'].'航班'.'参数错误，没有离港站']);
        }
        $dep_apt_zh = self::where('dep_apt', '=', $dep_apt)
            ->find();
        if (!$dep_apt_zh) {
            return '新航段';
//            throw new FlightRouteException(['msg' => $data['flt_id'].'航班'.'的
//            离港站为'.$dep_apt.',这是新航段，数据库中不存在']);
        }
        return $dep_apt_zh;
    }

    public static function getAll($page, $listRows)
    {
        $list = self::where('dep_apt', '<>', '')->paginate($listRows);
//        $list = Db::table('f_route')->page($page, $listRows)->select();
        return $list;
    }
}