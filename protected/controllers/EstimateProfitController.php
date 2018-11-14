<?php

use ddd\Profit\Application\InvoiceEventService;

/**
 * Desc: 预估利润报表
 * User: wwb
 * Date: 2018/6/14 0022
 * Time: 16:42
 */
class EstimateProfitController extends Controller
{
    public function pageInit() {
        $this->filterActions = "index,export,test,inputInvoiceInit,createHistoryData1,createHistoryData2,createHistoryData3,createHistoryData4,buyContractPass,sellContractPass,stockNoticeSettle,deliveryOrderSettle";
        $this->rightCode = "estimateProfit";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        $params = Mod::app()->request->getParam('search');

        $sql = 'select {col} from t_estimate_project_profit a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_project c on a.project_id=c.project_id
                left join t_system_user d on d.user_id=c.manager_user_id' . $this->getWhereSql($params) . '  and ' . AuthorizeService::getUserDataConditionString('c') . ' order by a.project_id desc {limit}';

        $fields = 'a.*,b.name as corporation_name,c.project_code,c.type,d.name manager_user_name';
        $data = $this->queryTablesByPage($sql, $fields);

        $this->render("index", $data);
    }

    public function actionExport() {
        $params = Mod::app()->request->getParam('search');

        $fields = "b.name 交易主体,
                   c.project_code 项目编号,
                   d.name 业务员,
                   (case when c.type=1 then '进口自营' when c.type=2 then '进口代采' when c.type=3 then '进口渠道' when c.type=4 then '内贸自营' when c.type=5 then '内贸代采' when c.type=6 then '内贸渠道' else '仓单质押' end) 项目类型,
                   a.sell_quantity 未完结预估销售数量（吨）,
                   round(ifnull(a.sell_amount,0)/100, 2) 为完结预估销售金额（元）,
                   round(ifnull(a.buy_price,0)/100, 2) 实际采购单价（元）,
                   round(ifnull(a.buy_amount,0)/100, 2) 实际采购金额（元）,
                   round(ifnull(a.invoice_amount,0)/100, 2) 已收票金额（元）,
                   round(ifnull(a.gross_profit,0)/100, 2) 预估毛利（元）,
                   '-' 预估运费（元）,
                   '-' 预估仓储费（元）,
                   '-' 预估仓杂费（元）,
                   round(ifnull(a.added_tax,0)/100, 2) 增值税（元）,
                   round(ifnull(a.surtax,0)/100, 2) 附加税（元）,
                   round(ifnull(a.stamp_tax,0)/100, 2) 印花税（元）,
                   round(ifnull(a.post_profit,0)/100, 2) 税后毛利（元）,
                   round(ifnull(a.fund_cost,0)/100, 2) 资金成本（元）,
                   round(ifnull(a.actual_profit,0)/100, 2) 业务净利润（元）

                  ";

        $sql = 'select ' . $fields . 'from t_estimate_project_profit a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_project c on a.project_id=c.project_id
                left join t_system_user d on d.user_id=c.manager_user_id ' . $this->getWhereSql($params) . '  and '. AuthorizeService::getUserDataConditionString('c') . ' order by a.project_id desc';

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }


    //历史数据
    public function actionCreateHistoryData1() {
        //采购合同
        $sql=" select a.contract_id
                              from
                              t_contract a
                              left join t_check_detail b on a.contract_id = b.obj_id and b.business_id=3
                      where  a.type=1 and a.status>=".Contract::STATUS_BUSINESS_CHECKED." order by b.update_time asc,a.contract_id asc";

        $contract  = Utility::query($sql);
        if(!empty($contract)){
            foreach($contract as $key=>$value){

                AMQPService::publishBuyContractBusinessCheckPass($value['contract_id']);
            }
        }

        echo '1、采购合同数据生成成功';
    }
    //历史数据
    public function actionCreateHistoryData2() {
        //销售合同价格
        $sql=" select a.contract_id
                              from
                              t_contract a
                              left join t_check_detail b on a.contract_id = b.obj_id and b.business_id=3
                      where  a.type=2 and a.status>=".Contract::STATUS_BUSINESS_CHECKED." order by b.update_time asc,a.contract_id asc";

        $contract  = Utility::query($sql);
        if(!empty($contract)){
            foreach($contract as $key=>$value){

                AMQPService::publishSellContractBusinessCheckPass($value['contract_id']);
            }
        }

        echo '2、销售合同数据生成成功';
    }
    //历史数据
    public function actionCreateHistoryData3() {

        //入库通知单结算
        $batch  = Utility::query("select batch_id from t_stock_in_batch where status>=".StockNotice::STATUS_SETTLED.' order by status_time asc');
        if(!empty($batch)){
            foreach($batch as $key=>$value){

                $service = new \ddd\Profit\Application\Estimate\EstimateProfitEventService();
                $service->onBatchSettlePass($value['batch_id']);
            }
        }


        echo '3、入库通知单结算数据生成成功';
    }
    //历史数据
    public function actionCreateHistoryData4() {

        //发货单结算
        $order  = Utility::query("select order_id from t_delivery_order where status>=".DeliveryOrder::STATUS_SETTLE_PASS.' order by status_time asc');
        if(!empty($order)){
            foreach($order as $key=>$value){
                $service = new \ddd\Profit\Application\Estimate\EstimateProfitEventService();
                $service->onDeliverySettlePass($value['order_id']);
            }
        }

        echo '4、发货单结算数据生成成功';
    }

    public function actionBuyContractPass(){
        set_time_limit(0);
        $project_id = $_GET['project_id'];
        if(empty($project_id)) {
            echo '失败，project_id为空';die;
        }
        //采购合同
        $sql=" select a.contract_id
                              from
                              t_contract a
                              left join t_check_detail b on a.contract_id = b.obj_id and b.business_id=3
                      where a.project_id=".$project_id." and a.type=1 and a.status>=".Contract::STATUS_BUSINESS_CHECKED." order by b.update_time asc,a.contract_id asc";

        $contract  = Utility::query($sql);
        if(!empty($contract)){
            foreach($contract as $key=>$value){
                echo $value['contract_id'].'、';
                AMQPService::publishBuyContractBusinessCheckPass($value['contract_id']);
                //sleep(10);
            }
        }

        echo "<br />".$project_id."项目下采购合同数据生成成功";
    }

    public function actionSellContractPass(){
        set_time_limit(0);
        $project_id = $_GET['project_id'];
        if(empty($project_id)) {
            echo '失败，project_id为空';die;
        }
        //销售合同
        $sql=" select a.contract_id
                              from
                              t_contract a
                              left join t_check_detail b on a.contract_id = b.obj_id and b.business_id=3
                      where a.project_id=".$project_id." and a.type=2 and a.status>=".Contract::STATUS_BUSINESS_CHECKED." order by b.update_time asc,a.contract_id asc";

        $contract  = Utility::query($sql);
        if(!empty($contract)){
            foreach($contract as $key=>$value){
                echo $value['contract_id'].'、';
                AMQPService::publishSellContractBusinessCheckPass($value['contract_id']);
                //sleep(10);
            }
        }

        echo "<br />".$project_id.'项目下销售合同数据生成成功';
    }

    public function actionStockNoticeSettle(){
        set_time_limit(0);
        $project_id = $_GET['project_id'];
        if(empty($project_id)) {
            echo '失败，project_id为空';die;
        }
        //入库通知单结算
        $batch  = Utility::query("select batch_id from t_stock_in_batch where project_id=".$project_id." and status>=".StockNotice::STATUS_SETTLED.' order by status_time asc');
        if(!empty($batch)){
            foreach($batch as $key=>$value){
                echo $value['batch_id'].'、';
                $service = new \ddd\Profit\Application\Estimate\EstimateProfitEventService();
                $service->onBatchSettlePass($value['batch_id']);
                //sleep(20);
            }
        }


        echo "<br />".$project_id.'项目下入库通知单结算数据生成成功';
    }

    public function actionDeliveryOrderSettle(){
        set_time_limit(0);
        $project_id = $_GET['project_id'];
        if(empty($project_id)) {
            echo '失败，project_id为空';die;
        }
        //发货单结算
        $order  = Utility::query("select order_id from t_delivery_order where project_id=".$project_id." and status>=".DeliveryOrder::STATUS_SETTLE_PASS.' order by status_time asc');
        if(!empty($order)){
            foreach($order as $key=>$value){
                echo $value['order_id'].'、';
                $service = new \ddd\Profit\Application\Estimate\EstimateProfitEventService();
                $service->onDeliverySettlePass($value['order_id']);
                //sleep(20);
            }
        }

        echo "<br />".$project_id.'项目下发货单结算数据生成成功';
    }

    //测试
    public function actionTest() {

        $buy_contract_id =1410;//采购
        $contract_id =1411 ;//销售
        $order_id=201809290001;
        $batch_id=201808150001;
        $project_id=20180930002;
        $corporation_id=4;

        $service = new \ddd\Profit\Application\Estimate\EstimateProfitEventService();

        //$service->onContractSettlePass($contract_id);
        //$service->onBuyContractSettlePass($buy_contract_id);
        //$service->onDeliverySettlePass($order_id);
        //$service->onBatchSettlePass($batch_id);

        /*$entity = \ddd\infrastructure\DIService::getRepository(\ddd\Profit\Domain\Model\Settlement\IBuyContractSettlementRepository::class)->findByContractId($contract_id);
        print_r($entity);*/

       /* $project_id=20171226001;
        $profitService  = new \ddd\Profit\Application\Estimate\EstimateProfitService();
        $profitService->createEstimateProjectProfit($project_id);*/

        /*$corporation_id=7;
        $profitService  = new \ddd\Profit\Application\Estimate\EstimateProfitService();
        $profitService->createEstimateCorporationProfit($corporation_id);*/

        //价格服务
        $service =  new \ddd\Profit\Application\Price\BuyPriceService();
        $service->onBuyContractPriceChange(1530);

        //数量服务
       /* $service =  new \ddd\Profit\Application\QuantityService();
        $service->onSellQuantityChange(201810260004);//201810250013*/

        //创建合同利润
        /*$service =  new \ddd\Profit\Application\Estimate\EstimateProfitService();
        $service->createEstimateContractProfit($buy_contract_id);*/
        //AMQPService::publishEstimateContractProfit($project_id);
        //$service->createEstimateProjectProfit($project_id);
        //$service->createEstimateCorporationProfit($corporation_id);

        //第一步
        //AMQPService::publishBuyContractBusinessCheckPass($buy_contract_id);
        //sleep(15);
        //AMQPService::publishSellContractBusinessCheckPass(1411);

        //第二步 采购合同结算
        //$service->onBuyContractSettlePass($buy_contract_id);

        //第三步 销售合同结算
        //$service->onContractSettlePass($contract_id);

        /*$service = new \ddd\Profit\Application\Estimate\EstimateProfitService();
        $service->createEstimateContractProfit(1916);*/
    }

    /**
     * 收票金额初始化
     */
    public function actionInputInvoiceInit() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        //发票利润修复
        $invoiceService = new InvoiceEventService();
        //进项票修复
        $sql = 'SELECT apply_id,contract_id FROM t_invoice_application WHERE status>=' . InvoiceApplication::STATUS_PASS
            . ' AND type_sub=' . InvoiceApplication::SUB_TYPE_GOODS . ' AND type=' . InvoiceApplication::TYPE_BUY;
        $invoiceApplyList = Utility::query($sql);
        if (Utility::isNotEmpty($invoiceApplyList)) {
            foreach ($invoiceApplyList as $apply) {
                $invoiceService->onInputInvoiceCheckPass($apply['contract_id'], $apply['apply_id']);
            }
        }
        echo 'Contract Pay Profit Init Success';
    }

}