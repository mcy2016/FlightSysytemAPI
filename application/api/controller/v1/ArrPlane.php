<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/9/19
 * Time: 13:44
 */

namespace app\api\controller\v1;


use app\api\service\Oracle_con;

class ArrPlane
{
    /**
     * 获取Oracle中的进港航班数据信息，并关联写入到f_arr_plane表和f_attr表中
     * @return \think\response\Json
     */
    public function getOmisData(){
        //获取Oracle中的进港航班数据信息
        //保存航班信息，写入f_arr_plane和f_attr表中
        //更新f_arr_plane和f_attr表中的航班信息，也就是更新所有进港航班信息及其属性
        $arrFlights=Oracle_con::getCurrentArrFlightByOmis();
        return json($arrFlights);
    }

    /**
     * 获取Oracle中的所有航班信息，并保存到航班信息数据库中（f_main）中
     * @return \think\response\Json
     */
    public function saveAllFlights(){
        $allFlights=Oracle_con::getAllFlightByOmis();//TODO 保存到main表中是连续2天的所有航班信息，没有返回到前端
        return json($allFlights);
    }
}