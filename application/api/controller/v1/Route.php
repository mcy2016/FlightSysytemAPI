<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/4/11
 * Time: 9:16
 */

namespace app\api\controller\v1;

use app\api\model\Route as RouteModel;
use think\Request;

class Route
{
    /**
     * 获取所有的航段信息，包括飞行时间
     *
     */
    public function getAll($page, $listRows)
    {
        $Route = new RouteModel();
        $result = $Route::getAll($page, $listRows);
        return json($result);
    }

    /**
     * 更新和新增航段，飞行时间
     */
    public function updateRoute()
    {
        $result = [];
        $request = Request::instance();
        $routeParam = $request->param();
        $rs = RouteModel::updateRoute($routeParam);
        if (!$rs) {
            $result['code'] = 0;
            $result['data'] = [];
            $result['msg'] = '失败';
        }
        $result['code'] = 1;
        $result['data'] = $rs;
        $result['msg'] = '成功';
        return json($result);
    }
}