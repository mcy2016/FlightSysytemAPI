<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/5/11
 * Time: 20:11
 */

namespace app\api\controller\v1;


class Hello
{
    public function hello()
    {
        $rs = [];
        $rs['url'] = 'http://172.18.161.13/hello.html';
        $rs['flag'] = true;
        $rs['time'] = 3000;
        $rs['opacity'] = 0.9;
        $rs['title'] = '大家工作愉快，身体健康!';
        return json($rs);
    }
}