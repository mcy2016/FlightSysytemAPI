<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/12/13
 * Time: 14:18
 */

namespace app\api\model;


use app\api\lib\exception\UserException;
use Exception;
use think\Model;

class User extends Model
{
    public static function getUserById($id)
    {
        $user = self::where('id', '=', $id)
            ->find();
        return $user;
    }

    public static function getUserByName($name)
    {
        $user = self::where('username', '=', $name)->find();
        return $user;
    }

    //根据状态获取人员信息
    public static function getDutyUser($status, $condition)
    {
        $dutyUser = self::where('status', $condition, $status)
            ->order('convert(username using gb2312) asc')
            ->select();
        return $dutyUser;
    }

    public static function getUserByCard($cardId)
    {
        $user = self::where('card_id', '=', $cardId)
            ->find();
        return $user;
    }

    //用户登录业务逻辑
    public static function login($data)
    {
        if (!array_key_exists('username', $data) || !array_key_exists('username', $data)) {
            throw new UserException(['msg' => '用户名或密码不能为空', 'errorCode' => 40001]);
        }
        $userName = $data['username'];
        $password = md5($data['password']);
        $user = self::where('username', '=', $data['username'])
            ->find();
        if (!$user) {
            throw new UserException(
                ['msg' => '用户名错误', 'errorCode' => 40005]
            );
        }
        if ($userName !== $user->username) {
            throw new UserException(
                ['msg' => '用户名错误', 'errorCode' => 40005]
            );
        }
        if ($password !== $user->password) {
            throw new UserException(
                ['msg' => '密码错误', 'errorCode' => 40006]
            );
        }
        if ($user->scope < 16) {
            throw new UserException(
                ['msg' => '用户权限不足', 'errorCode' => 9999]
            );
        }
        return $user;
    }

    //注册业务逻辑
    public static function regist()
    {

    }

    public static function updateUser($data)
    {
        $user = self::where('id', '=', $data['user_id'])->find();
        $user->card_id = $data['card_id'];
        $user->phone = $data['phone'];
        $user->profession = $data['profession'];
        $user->status = $data['status'];
        $user->group = array_key_exists('group', $data) ? $data['group'] : '';
        $user->rank = $data['rank'];
        try {
            //捕获异常
            $rs = $user->save();
        } catch (Exception $ex) {
            //TODO: 可以记录日志
        }
        return $rs;
    }

    public static function newUser($data)
    {
        $user = new User();
        $user->username = $data['username'];
        $user->card_id = $data['card_id'];
        $user->phone = $data['phone'];
        $user->profession = $data['profession'];
//        $user->status = $data['status'];
        $user->group = $data['group'];
        $user->rank = $data['rank'];
        try {
            //捕获异常
            $rs = $user->save();
        } catch (Exception $ex) {
            //TODO: 可以记录日志
        }
        return $rs;
    }
}