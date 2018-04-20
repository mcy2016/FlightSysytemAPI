<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/9/19
 * Time: 15:24
 */

namespace app\api\lib\exception;


class OmisException extends BaseException
{
    public $code=404;
    public $msg='航班从OMIS数据库导入错误';
    public $errorCode=80000;
}