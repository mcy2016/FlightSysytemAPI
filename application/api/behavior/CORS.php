<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/20
 * Time: 9:00
 */

namespace app\api\behavior;

class CORS
{
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET');
        if (request()->isOptions()) {
            exit();
        }
    }
}