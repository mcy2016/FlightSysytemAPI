<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/4/11
 * Time: 9:16
 */

namespace app\api\controller\v1;

use app\api\model\Route as RouteModel;

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

    }
}