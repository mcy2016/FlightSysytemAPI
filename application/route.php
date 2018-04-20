<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//Route::get('api/:verison/test','api/:verison.Test/test');

//Omis相关
Route::get('api/:verison/OmisArr', 'api/:verison.ArrPlane/getOmisData');
Route::get('api/:verison/All', 'api/:verison.ArrPlane/saveAllFlights');

//进港航班信息 Flight
Route::get('api/:verison/plane', 'api/:verison.Flight/getArrFlight');
Route::get('api/:verison/mcc_plane', 'api/:verison.Flight/getArrFlightMcc');
Route::get('api/:verison/query_plane', 'api/:verison.Flight/getArrFlightByDate');
Route::get('api/:verison/closed', 'api/:verison.Flight/getCloseFlight');
Route::get('api/:verison/all_plane', 'api/:verison.Flight/getArrFlightAll');
Route::post('api/:verison/recover', 'api/:verison.Flight/recoverArrFlight');
Route::post('api/:verison/update', 'api/:verison.Flight/updateArrFlight');
Route::post('api/:verison/nextTime', 'api/:verison.Flight/updateNextTime');
Route::post('api/:verison/fltId', 'api/:verison.Flight/updateFltId');
Route::post('api/:verison/acType', 'api/:verison.Flight/updateAcType');
Route::post('api/:verison/acId', 'api/:verison.Flight/updateAcId');
Route::post('api/:verison/offTime', 'api/:verison.Flight/updateOffTime');
Route::post('api/:verison/acStopArr', 'api/:verison.Flight/updateAcStopArr');
Route::post('api/:verison/acStatus', 'api/:verison.Flight/updateAcStatus');

// 更新除冰信息
Route::post('api/:verison/updateDei', 'api/:verison.Flight/updateDei');
Route::post('api/:verison/new', 'api/:verison.Flight/saveArrFlight');
Route::get('api/:verison/close/:id', 'api/:verison.Flight/deleteArrFlight');
Route::get('api/:verison/before', 'api/:verison.Flight/newBeforeFlight');

//用户相关
Route::get('api/:verison/users/:id', 'api/:verison.User/getById');
Route::get('api/:verison/all_user', 'api/:verison.User/getAllUser');
Route::get('api/:verison/user', 'api/:verison.User/getDutyUser');
Route::post('api/:verison/card', 'api/:verison.User/cardUser');
Route::post('api/:verison/handCard', 'api/:verison.User/handCardUser');
Route::post('api/:verison/delete', 'api/:verison.User/deleteDuty');
Route::post('api/:verison/next', 'api/:verison.User/nextDuty');
Route::post('api/:verison/updateUser', 'api/:verison.User/updateUser');
Route::post('api/:verison/updateDuty', 'api/:verison.UserLog/updateDutyUser');

// 工时出勤
Route::get('api/:verison/work_hours', 'api/:verison.UserLog/shiftByProfession');

// 飞行时间、航段相关
Route::get('api/:verison/route_all', 'api/:verison.Route/getAll');

//注册、登录
Route::post('api/:verison/login', 'api/:verison.Login/login');