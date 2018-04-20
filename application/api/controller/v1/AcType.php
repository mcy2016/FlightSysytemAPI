<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/1/15
 * Time: 20:42
 */

namespace app\api\controller\v1;

use app\api\lib\exception\AcTypeException;
use app\api\model\AcidType as AcidTypeModel;
use think\Request;

class AcType
{
    /**
     *获取所有的机型机号数据
     */
    public function getAll()
    {
        $result = AcidTypeModel::getAll();
        return json($result);
    }


    public function getByAcId($acid = '')
    {
        $request = Request::instance();
        $paramAcId = $request->param('ac_id');
        $result = AcidTypeModel::getByAcId($paramAcId);
        if (!$result) {
            throw new AcTypeException(['msg' => '该机号对应的机型数据不存在']);
        }
        return json($result);
    }
}