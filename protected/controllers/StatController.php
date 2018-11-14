<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/13 16:45
 * Describe：
 */

class StatController extends Controller
{
    public $statId="";

    public function pageInit()
    {
        parent::pageInit();
        $this->statId=Mod::app()->request->getParam("id");
        if(empty($this->statId))
        {
            $this->statId=$_GET["search"]["id"];
        }
        $this->rightCode ="stat_".$this->statId;
    }
    public function actionIndex()
    {
        $search=$_GET["search"];
        if(empty($this->statId))
            $this->statId=$search["id"];
        unset($search["id"]);

        if(empty($this->statId))
            $this->renderError("参数错误");

        $config=StatService::getConfig($this->statId);
        if(empty($config))
            $this->renderError("图表配置不存在");
        $fn=$config["fn"];
        if(empty($fn))
            $this->renderError("数据源方法错误");
        $data=StatService::$fn();
        $this->render('index',array(
            "data"=>$data,
        ));
    }

    public function actionPayment()
    {
        $payData=StatService::getPaymentStat();
        $this->render('payment',array(
            "title"=>"",
            "payData"=>$payData,
        ));
    }

}