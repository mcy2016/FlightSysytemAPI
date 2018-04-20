<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/14
 * Time: 14:03
 */

namespace app\api\lib\exception;


class FlightException extends BaseException
{
    public $code = 401;
    public $msg = '没有找到此航班的相关信息';
    public $errorCode = 30001;
}