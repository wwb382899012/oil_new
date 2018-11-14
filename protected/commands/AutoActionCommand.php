<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/11/11
 * Time: 15:26
 * Describe：
 *      定时执行类
 */
class AutoActionCommand extends CConsoleCommand
{

    /**
     * 计算分配数据
     */
    public function actionComputeProfit()
    {
        $date=Utility::getDate("-1 days");
        ProfitService::computeProfit($date);
    }


    public function actionTest()
    {

    }



    public function actionAutoEveryDay()
    {

    }

    public function actionAutoEveryHour()
    {
        Mod::log("Start auto task for hour >>> ");

        StatTimelinessService::generateData();

        Mod::log("End auto task for hour. ");
    }

    public function actionAutoEveryMinute()
    {

    }





}