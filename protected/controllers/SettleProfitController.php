<?php

/**
 * Desc: 结算利润报表
 * User: wwb
 * Date: 2018/6/14 0022
 * Time: 16:42
 */
class SettleProfitController extends Controller
{
    public function pageInit() {
        $this->filterActions = "index,export,createStockNoticeCost,createBuyGoodsCost,createDeliveryOrderProfit,addStockNoticeCost,addBuyGoodsCost,addDeliveryOrderProfit,addContractProfit,addProjectProfit,addCorporationProfit,contractPayInit,projectPayInit,dataRepair";
        $this->rightCode = "settleProfit";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        $params = Mod::app()->request->getParam('search');

        $sql = 'select {col} from t_project_settle_profit a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_project c on a.project_id=c.project_id
                left join t_system_user d on d.user_id=c.manager_user_id' . $this->getWhereSql($params) . ' and a.settle_quantity>0 and ' . AuthorizeService::getUserDataConditionString('c') . ' order by a.project_id desc {limit}';

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
                   a.settle_quantity 结算销售数量（吨）,
                   round(ifnull(a.settle_amount,0)/100, 2) 结算销售金额（元）,
                   round(ifnull(a.sell_invoice_amount,0)/100, 2) 已开票金额（元）,
                   round(ifnull(a.buy_price,0)/100, 2) 实际采购单价（元）,
                   round(ifnull(a.buy_amount,0)/100, 2) 实际采购金额（元）,
                   round(ifnull(a.buy_invoice_amount,0)/100, 2) 已收票金额（元）,
                   round(ifnull(a.pay_amount,0)/100, 2) 已付上游款（元）,
                   round(ifnull(a.receive_amount,0)/100, 2) 已收下游款（元）,
                   round(ifnull(a.actual_gross_profit,0)/100, 2) 实际毛利（元）,
                   '-' 运费（元）,
                   '-' 仓储费（元）,
                   round(ifnull(a.miscellaneous_fee,0)/100, 2) 杂费（元）,
                   round(ifnull(a.vat,0)/100, 2) 增值税（元）,
                   round(ifnull(a.sur_tax,0)/100, 2) 附加税（元）,
                   round(ifnull(a.stamp_tax,0)/100, 2) 印花税（元）,
                   round(ifnull(a.after_tax_profit,0)/100, 2) 税后毛利（元）,
                   round(ifnull(a.fund_cost,0)/100, 2) 资金成本（元）,
                   round(ifnull(a.profit,0)/100, 2) 业务净利润（元）

                  ";

        $sql = 'select ' . $fields . 'from t_project_settle_profit a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_project c on a.project_id=c.project_id
                left join t_system_user d on d.user_id=c.manager_user_id ' . $this->getWhereSql($params). ' and a.settle_quantity>0 and ' . AuthorizeService::getUserDataConditionString('c') . ' order by a.project_id desc';

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }

    /**
     * @name:actionCreateStockNoticeCost
     * @desc:发货单利润 初始化
     * @param:
     * @throw: * @throws Exception
     * @throws \ddd\infrastructure\error\ZEntityNotExistsException
     * @throws \ddd\infrastructure\error\ZException
     * @return:void
     */
    public function actionCreateStockNoticeCost() {
        set_time_limit(0);
        $stockNoticeList = StockNotice::model()->findAll();
        if (!empty($stockNoticeList)) {
            foreach ($stockNoticeList as & $value) {
                //if($value->batch_id=='201808230001') {
                $batch_id = $value->batch_id;
                //入库通知单成本 初始化
                $service = new \ddd\Profit\Application\ProfitEventService();
                $service->onBatchSettlePass($batch_id);

                //}
            }
        }

    }

    /**
     * @name:actionCreateBuyGoodsCost
     * @desc:采购商品成本 初始化
     * @param:
     * @throw: * @throws Exception
     * @throws \ddd\infrastructure\error\ZEntityNotExistsException
     * @throws \ddd\infrastructure\error\ZException
     * @return:void
     */
    public function actionCreateBuyGoodsCost() {
        set_time_limit(0);
        //$deliveryOrderList = DeliveryOrder::model()->findAll();
        $sql = "select order_id from t_delivery_order where status >=" . DeliveryOrder::STATUS_SETTLE_PASS;
        $deliveryOrderList = Utility::query($sql);
        if (!empty($deliveryOrderList)) {
            foreach ($deliveryOrderList as & $value) {
                //if($value['order_id']=='201808230001') {
                $order_id = $value['order_id'];//201807070040 已结算，已有利润值   201807060011

                //采购商品明细 初始化
                $deliveryOrder = \ddd\infrastructure\DIService::getRepository(\ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository::class)->findByPk($order_id);
                $buyGoodsCost = new \ddd\Profit\Domain\Model\Stock\BuyGoodsCost();
                $buyGoodsCost = $buyGoodsCost->create($deliveryOrder);
                if (!empty($buyGoodsCost)) {
                    foreach ($buyGoodsCost as & $item) {
                        \ddd\infrastructure\DIService::getRepository(\ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository::class)->store($item);
                    }
                }


                //}
            }
        }

    }

    /**
     * @name:actionCreateDeliveryOrderProfit
     * @desc:发货单利润 初始化
     * @param:
     * @throw: * @throws Exception
     * @throws \ddd\infrastructure\error\ZEntityNotExistsException
     * @throws \ddd\infrastructure\error\ZException
     * @return:void
     */
    public function actionCreateDeliveryOrderProfit() {
        set_time_limit(0);
        $sql = "select order_id from t_delivery_order where status >=" . DeliveryOrder::STATUS_SETTLE_PASS;
        $deliveryOrderList = Utility::query($sql);
        //$deliveryOrderList = DeliveryOrder::model()->findAll();
        if (!empty($deliveryOrderList)) {
            foreach ($deliveryOrderList as & $value) {
                //if($value['order_id']=='201808170025') {
                $order_id = $value['order_id'];//201807070040 已结算，已有利润值   201807060011

                //发货单利润报表 初始化
                $deliveryOrderProfit = \ddd\Profit\Application\ProfitService::service()->createDeliveryOrderProfit($order_id);

               // }
            }
        }

    }

    //入库通知单成本 单条记录修复
    public function actionAddStockNoticeCost() {
        set_time_limit(0);
        $batch_id = $_GET['batch_id'];
        //入库通知单成本 初始化
        $service = new \ddd\Profit\Application\ProfitEventService();
        $service->onBatchSettlePass($batch_id);

    }

    //采购商品成本 单条记录修复
    public function actionAddBuyGoodsCost() {
        set_time_limit(0);
        $order_id = $_GET['order_id'];
        //采购商品明细 初始化
        $deliveryOrder = \ddd\infrastructure\DIService::getRepository(\ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository::class)->findByPk($order_id);
        $buyGoodsCost = new \ddd\Profit\Domain\Model\Stock\BuyGoodsCost();
        $buyGoodsCost = $buyGoodsCost->create($deliveryOrder);
        if (!empty($buyGoodsCost)) {
            foreach ($buyGoodsCost as & $item) {
                \ddd\infrastructure\DIService::getRepository(\ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository::class)->store($item);
            }
        }

    }

    //发货单利润 单条记录修复
    public function actionAddDeliveryOrderProfit() {
        $order_id = $_GET['order_id'];
        //$order_id = '201802080011';//201807070040 已结算，已有利润值   201807060011
        \ddd\Profit\Application\ProfitService::service()->createDeliveryOrderProfit($order_id);
    }

    //项目利润 单条记录修复
    public function actionAddProjectProfit() {
        $project_id = $_GET['project_id'];
        \ddd\Profit\Application\ProfitService::service()->addProjectProfitByProjectId($project_id);
    }

    //项目利润 单条记录修复
    public function actionAddContractProfit() {
        $contract_id = $_GET['contract_id'];
        \ddd\Profit\Application\ProfitService::service()->addProjectProfit($contract_id);
    }

    //交易主体 单条记录修复
    public function actionAddCorporationProfit() {
        $corporation_id = $_GET['corporation_id'];
        \ddd\Profit\Application\ProfitService::service()->addCorporationProfitByCorporationId($corporation_id);
    }



    /**
     * 合同下付款利润初始化
     */
    public function actionContractPayInit() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $sql = "select
                    a.contract_id,a.type
              from
                    t_contract a
              where a.status >=" . Contract::STATUS_BUSINESS_CHECKED . "
              order by a.contract_id asc";
        $contractList = Utility::query($sql);
        $service = new \ddd\Profit\Application\PayReceiveEventService();
        if (Utility::isNotEmpty($contractList)) {
            foreach ($contractList as $contract) {
                $service->contractProfitDataRepair($contract['contract_id']);
            }
        }
        //发票利润修复
        $invoiceService = new \ddd\Profit\Application\InvoiceEventService();
        //进项票修复
        $sql = 'SELECT apply_id,contract_id FROM t_invoice_application WHERE status>=' . InvoiceApplication::STATUS_PASS
            . ' AND type_sub=' . InvoiceApplication::SUB_TYPE_GOODS . ' AND type=' . InvoiceApplication::TYPE_BUY;
        $invoiceApplyList = Utility::query($sql);
        if (Utility::isNotEmpty($invoiceApplyList)) {
            foreach ($invoiceApplyList as $apply) {
                $invoiceService->contractProfitDataRepair($apply['contract_id'], $apply['apply_id']);
            }
        }
        //开票数据修复
        $sql = 'SELECT a.invoice_id,a.contract_id FROM t_invoice a LEFT JOIN t_invoice_application b on a.apply_id=b.apply_id WHERE a.status>=' . Invoice::STATUS_PASS
            . ' AND b.type_sub=' . InvoiceApplication::SUB_TYPE_GOODS . ' AND b.type=' . InvoiceApplication::TYPE_SELL;
        $invoiceList = Utility::query($sql);
        if (Utility::isNotEmpty($invoiceList)) {
            foreach ($invoiceList as $invoice) {
                $invoiceService->contractProfitDataRepair($invoice['contract_id'], 0, $invoice['invoice_id']);
            }
        }

        echo 'Contract Pay Profit Init Success';
    }

    /**
     * 项目下付款利润初始化
     */
    public function actionProjectPayInit() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $sql = "SELECT a.project_id, b.amount, b.type, c.payment_id FROM t_project a 
	            LEFT JOIN t_pay_application b ON a.project_id = b.project_id AND b.type =" . PayApplication::TYPE_PROJECT . "
                LEFT JOIN t_payment c ON b.apply_id = c.apply_id
                WHERE c.payment_id is not null AND a.status>=" . Payment::STATUS_SUBMITED;
        $projectList = Utility::query($sql);
        $service = new \ddd\Profit\Application\ProjectPayEventService();
        if (Utility::isNotEmpty($projectList)) {
            foreach ($projectList as $project) {
                $service->onPayConfirm($project['project_id'], $project['payment_id']);
            }
        }
        echo 'Project Pay Profit Init Success';
    }


    /**
     * 修复 收付款和发票的事件数据
     */
    public function actionDataRepair() {
        $type = Mod::app()->request->getParam("type", 'input_invoice');
        $contractId = Mod::app()->request->getParam("contract_id", 0);
        $projectId = Mod::app()->request->getParam("project_id", 0);
        $paymentId = Mod::app()->request->getParam("payment_id", 0);
        $invoiceId = Mod::app()->request->getParam("invoice_id", 0);
        $invoiceApplicationId = Mod::app()->request->getParam("invoice_application_id", 0);
        $subject = Mod::app()->request->getParam("subject", 0);
        try {
            switch ($type) {
                case 'input_invoice'://进项票事件修复
                    $res = \ddd\Profit\Application\InvoiceEventService::service()->onInputInvoiceCheckPass($contractId, $invoiceApplicationId);
                    break;
                case 'invoice'://开票事件修复
                    $res = \ddd\Profit\Application\InvoiceEventService::service()->onInvoiceCheckPass($contractId, $invoiceId);
                    break;
                case 'receive_confirm'://收付款事件修复
                    $res = \ddd\Profit\Application\PayReceiveEventService::service()->onReceiveConfirm($contractId, $subject);
                    break;
                case 'pay_confirm':
                    $res = \ddd\Profit\Application\PayReceiveEventService::service()->onPayConfirm($contractId, $subject);
                    break;
                case 'pay_claim':
                    $res = \ddd\Profit\Application\PayReceiveEventService::service()->onPayClaim($contractId, $subject);
                    break;
                case 'project_pay_confirm':
                    $res = \ddd\Profit\Application\ProjectPayEventService::service()->onPayConfirm($projectId, $paymentId);
                    break;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return;
        }
        echo 'Data Repair Success';
    }

}