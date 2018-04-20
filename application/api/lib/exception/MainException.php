<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/14
 * Time: 14:56
 */

namespace app\api\lib\exception;


class MainException extends BaseException
{
    public $code = 401;
    public $errorCode = 50001;
    public $msg = '该机号今日无航班';
}