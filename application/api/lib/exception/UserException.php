<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/13
 * Time: 14:23
 */

namespace app\api\lib\exception;


class UserException extends BaseException
{
    public $code=401;
    public $msg='没有此用户';
    public $errorCode=40001;
}