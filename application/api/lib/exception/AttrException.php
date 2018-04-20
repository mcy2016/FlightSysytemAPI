<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/1/25
 * Time: 15:52
 */

namespace app\api\lib\exception;


class AttrException extends BaseException
{
    public $code = 401;
    public $msg = '没有该机号对应的机型数据';
    public $errorCode = 70001;
}