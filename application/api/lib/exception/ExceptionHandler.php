<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/9/19
 * Time: 15:01
 */

namespace app\api\lib\exception;


use think\Exception;
use think\exception\Handle;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code='';
    private $msg='';
    private $errorCode='';

    //需要返回客户端当前请求的URL路径
    public function render(\Exception $e)
    {
        if($e instanceof BaseException){
            //如果是自定义异常类
            $this->code=$e->code;
            $this->msg=$e->msg;
            $this->errorCode=$e->errorCode;
        }else {
            //不是自定义异常类
            //根据app_debug的值，来确定返回什么样式的异常
            if(config('app_debug')){
                return parent::render($e);
            }else {
                $this->code=500;
                $this->msg  ='服务器内部错误';
                $this->errorCode=999;
                //记录异常日志
                //TODO
            }
        }
        $request=Request::instance();
        $result=[
            'msg'=>$this->msg,
            'errorCode'=>$this->errorCode,
            'request_url'=>$request->url()
        ];
        return json($result);
    }
}