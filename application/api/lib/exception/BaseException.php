<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/9/19
 * Time: 15:05
 */

namespace app\api\lib\exception;


use think\Exception;
use Throwable;

class BaseException extends Exception
{
    //HTTP状态码 200 400
    public $code = 400;
    //错误具体信息
    public $msg = '参数错误';
    //自定义错误码
    public $errorCode = '10000';

    public function __construct($params = [])
    {
        if (!is_array($params)) {
            return;//参数必须是数组
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
           $this->msg=$params['msg'];
        }
        if(array_key_exists('errorCode',$params)){
            $this->errorCode=$params['errorCode'];
        }
    }
}