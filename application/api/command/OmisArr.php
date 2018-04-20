<?php

namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\api\controller\v1\ArrPlane;
use app\api\service\Oracle_con;


/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2018/1/30
 * Time: 10:51
 */
class OmisArr extends Command
{
    protected function configure()
    {
        $this->setName('omisArr')->setDescription('请求omis进港航班数据');
    }

    protected function execute(Input $input, Output $output)
    {
        $res = Oracle_con::getAllFlightByOmis();
        if ($res) {
            $rs = Oracle_con::getCurrentArrFlightByOmis();
        }
        $count = count($rs);
        if ($rs) {
            $output->writeln("omisArr:$count");
        } else {
            $output->writeln("没有请求到omisArr数据");
        }
    }
}