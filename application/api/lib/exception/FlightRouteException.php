<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/11/15
 * Time: 16:27
 */

namespace app\api\lib\exception;


class FlightRouteException extends BaseException
{
    public $code=401;
    public $msg='航班航站信息错误';
    public $errorCode=10001;
}