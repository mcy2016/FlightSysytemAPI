<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/12
 * Time: 15:57
 */

namespace app\api\lib\exception;


class FlightAttrException extends BaseException
{
    public $code=401;
    public $msg='没有找到此航班的属性相关信息';
    public $errorCode=20001;
}