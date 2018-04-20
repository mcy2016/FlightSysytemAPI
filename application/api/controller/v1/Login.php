<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/14
 * Time: 12:13
 */

namespace app\api\controller\v1;


use think\Controller;
use think\Request;
use app\api\model\User as UserModel;

class Login extends Controller
{
    public function login(Request $request)
    {
        $result = [];
        $params = $request->post();
        $user = UserModel::login($params);
        if (!$user) {
            $result['msg'] = '登录失败';
            $result['code'] = -1;
            $result['data'] = null;
        }
        $result['msg'] = '登录成功';
        $result['code'] = 0;
        $result['data'] = $user;
        return json($result);
    }
}