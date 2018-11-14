<?php

/**
 * Desc: 风控合作方额度
 * User: susiehuang
 * Date: 2018/3/22 0022
 * Time: 16:42
 */
class RiskPartnerAmountController extends Controller
{
    public function pageInit()
    {
        $this->rightCode = 'riskPartnerAmount';
    }

    public function actionIndex()
    {
        $params = Mod::app()->request->getParam('search');
        $sql = 'select {col} from t_partner_stat ps 
                left join t_partner p on p.partner_id = ps.partner_id ' . $this->getWhereSql($params) . ' order by p.update_time desc {limit}';

        $fields = 'ps.partner_id, ps.amount_in, ps.amount_out, ps.goods_in_amount, ps.goods_in_settle_amount, ps.goods_in_unsettled_amount, ps.goods_out_amount, 
                   ps.goods_out_settle_amount, ps.goods_out_unsettled_amount, ps.amount_invoice_out, ps.amount_invoice_in, p.name as partner_name,
                   ifnull((select used_amount from t_partner_amount where partner_id = ps.partner_id and type = 1),0) as contract_used_amount,
                   ifnull((select used_amount from t_partner_amount where partner_id = ps.partner_id and type = 2),0) AS actual_used_amount,
                   (select ifnull(sum(amount_cny),0) from t_contract where type=1 and status >=10 and status <>20 and partner_id=ps.partner_id) as p_contract_used_amount,
                   (select ifnull(sum(amount_cny),0) from t_contract where type=2 and status >=10 and status <>20 and partner_id=ps.partner_id) as s_contract_used_amount';
        $data = $this->queryTablesByPage($sql, $fields);

        $this->render("index", $data);
    }

    public function actionExport()
    {
        $params = Mod::app()->request->getParam('search');

        $fields = 'p.name 合作方名称, 
                   concat(round(ifnull((select used_amount from t_partner_amount where partner_id = ps.partner_id and type = 1),0)/1000000, 2),"万元") 合同额度,
                   concat(round(ifnull((select used_amount from t_partner_amount where partner_id = ps.partner_id and type = 2),0)/1000000, 2),"万元") 实际占用额度,
                   concat(round((select ifnull(sum(amount_cny),0) from t_contract where type=1 and status >=10 and status <>20 and partner_id=ps.partner_id)/1000000, 2),"万元") 采购合同总金额,
                   concat(round((select ifnull(sum(amount_cny),0) from t_contract where type=2 and status >=10 and status <>20 and partner_id=ps.partner_id)/1000000, 2),"万元") 销售合同总金额,
                   concat(round(ps.goods_in_amount/1000000, 2),"万元") 入库单金额, concat(round(ps.goods_in_settle_amount/1000000, 2),"万元") 入库单结算金额, 
                   concat(round(ps.goods_in_unsettled_amount/1000000, 2),"万元") 入库单未结算金额, concat(round(ps.goods_out_amount/1000000, 2),"万元") 出库单金额, 
                   concat(round(ps.goods_out_settle_amount/1000000, 2),"万元") 出库单结算金额, concat(round(ps.goods_out_unsettled_amount/1000000, 2),"万元") 出库单未结算金额,
                   concat(round(ps.amount_out/1000000, 2),"万元") 已付货款金额, concat(round(ps.amount_in/1000000, 2),"万元") 已收货款金额, 
                   concat(round(ps.amount_invoice_in/1000000, 2),"万元") 已收票金额, concat(round(ps.amount_invoice_out/1000000, 2),"万元") 已开票金额';

        $sql = 'select ' . $fields . ' from t_partner_stat ps 
                left join t_partner p on p.partner_id = ps.partner_id ' . $this->getWhereSql($params) . ' order by p.update_time desc';

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }

    public function actionDetail()
    {
        $params = Mod::app()->request->getParam('search');
        if (!Utility::checkQueryId($params['a.partner_id']) || !Utility::checkQueryId($params['a.type']))
        {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $sql = 'select {col} from t_partner_amount_log a 
                left join t_contract b on b.contract_id = a.contract_id 
                left join t_corporation c on c.corporation_id = a.corporation_id 
                left join t_contract rc on rc.contract_id = b.relation_contract_id ' . $this->getWhereSql($params) . ' order by a.create_time desc {limit}';

        $fields = 'a.log_id, a.partner_id, a.method, a.amount, a.amount_total, a.corporation_id, a.project_id, a.contract_id, a.remark, a.create_time, 
                   (select ifnull(sum(amount_cny),0) from t_contract where contract_id=a.contract_id) as contract_amount,
                   b.contract_code, b.relation_contract_id, b.type as contract_type, b.category, b.amount_cny, c.name as corp_name, rc.contract_code as relation_contract_code';

        $data = $this->queryTablesByPage($sql, $fields);
        if (Utility::isNotEmpty($data['data']['rows']))
        {
            foreach ($data['data']['rows'] as $key => $row)
            {
                $data['data']['rows'][$key]['goods_settle_amount'] = ContractService::getContractGoodsSettlementAmount($row['contract_id']); //合同已结算金额
                if ($row['contract_type'] == ConstantMap::BUY_TYPE)
                {
                    $data['data']['rows'][$key]['goods_amount'] = ContractService::getContractGoodsActualPaidAmount($row['contract_id']);
                    $data['data']['rows'][$key]['stock_amount'] = ContractService::getContractStockInAmount($row['contract_id']);
                    $data['data']['rows'][$key]['invoice_amount'] = ContractService::getContractInputInvoiceAmount($row['contract_id']);
                    $data['data']['rows'][$key]['goods_unsettled_amount'] = ContractService::getTradeGoodsInUnsettledAmount($row['contract_id']);
                } else
                {
                    $data['data']['rows'][$key]['goods_amount'] = ReceiveConfirmService::getReceivedGoodsAmountByContractId($row['contract_id']);
                    $data['data']['rows'][$key]['stock_amount'] = ContractService::getContractStockOutAmount($row['contract_id']);
                    $data['data']['rows'][$key]['invoice_amount'] = ContractService::getContractOutputInvoiceAmount($row['contract_id']);
                    $data['data']['rows'][$key]['goods_unsettled_amount'] = ContractService::getTradeGoodsOutUnsettledAmount($row['contract_id']);
                }
            }
        }
        $partnerAmount = PartnerAmount::model()->find('partner_id = :partnerId and type = :type', array('partnerId' => $params['a.partner_id'], 'type' => $params['a.type']));
        $partnerAmountInfo['partner_id'] = $params['a.partner_id'];
        $partnerAmountInfo['partner_name'] = $partnerAmount->partner->name;
        $partnerAmountInfo['used_amount'] = $partnerAmount->used_amount;
        $partnerAmountInfo['type'] = $params['a.type'];
        $partnerAmountInfo['init_amount'] = $partnerAmount->remark;

        $this->pageTitle = $params['a.type'] == 1 ? '合同额度占用明细' : '实际占用额度明细';

        $data['search']['a.partner_id'] = $params['a.partner_id'];
        $data['search']['a.type'] = $params['a.type'];
        $this->render("detail", array('data' => $data, 'partnerAmountInfo' => $partnerAmountInfo));
    }

    public function actionDetailExport()
    {
        $params = Mod::app()->request->getParam('search');
        if (!Utility::checkQueryId($params['a.partner_id']) || !Utility::checkQueryId($params['a.type']))
        {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $fields = 'a.create_time 时间, round(a.method*a.amount/100, 2) 额度增减值, a.remark 变动因素, c.name 交易主体, a.contract_id 合同ID, b.contract_code 合同编号, 
                   b.type contract_type, b.relation_contract_id 关联合同ID, rc.contract_code 关联合同编号, b.contract_id, b.category,
                   (select ifnull(sum(amount_cny),0) from t_contract where contract_id=a.contract_id) 合同金额';

        $sql = 'select ' . $fields . ' from t_partner_amount_log a 
                left join t_contract b on b.contract_id = a.contract_id 
                left join t_corporation c on c.corporation_id = a.corporation_id 
                left join t_contract rc on rc.contract_id = b.relation_contract_id ' . $this->getWhereSql($params) . ' order by a.create_time desc';

        $data = Utility::query($sql);
        if (Utility::isNotEmpty($data))
        {
            foreach ($data as $key => $row)
            {
                $data[$key]['合同类型'] = Map::$v['contract_category'][$row['category']];
                $goodsSettleAmount = ContractService::getContractGoodsSettlementAmount($row['contract_id']); //合同已结算金额
                if ($row['contract_type'] == ConstantMap::BUY_TYPE)
                {
                    $paidAmount = ContractService::getContractGoodsActualPaidAmount($row['contract_id']);
                    $stockInAmount = ContractService::getContractStockInAmount($row['contract_id']);
                    $inputInvoiceAmount = ContractService::getContractInputInvoiceAmount($row['contract_id']);
                    $unsettledAmount = ContractService::getTradeGoodsInUnsettledAmount($row['contract_id']);
                    $data[$key]['已付货款/已收货款（金额）'] = round($paidAmount / 100, 2);
                    $data[$key]['入库单/出库单（金额）'] = round($stockInAmount / 100, 2);
                    $data[$key]['合同已结算金额'] = round($goodsSettleAmount / 100, 2);
                    $data[$key]['合同未结算金额'] = round($unsettledAmount / 100, 2);
                    $data[$key]['已收票/已开票(金额）'] = round($inputInvoiceAmount / 100, 2);
                } else
                {
                    $receivedAmount = ReceiveConfirmService::getReceivedGoodsAmountByContractId($row['contract_id']);
                    $stockOutAmount = ContractService::getContractStockOutAmount($row['contract_id']);
                    $outputInvoiceAmount = ContractService::getContractOutputInvoiceAmount($row['contract_id']);
                    $unsettledAmount = ContractService::getTradeGoodsOutUnsettledAmount($row['contract_id']);
                    $data[$key]['已付货款/已收货款（金额）'] = round($receivedAmount / 100, 2);
                    $data[$key]['入库单/出库单（金额）'] = round($stockOutAmount / 100, 2);
                    $data[$key]['合同已结算金额'] = round($goodsSettleAmount / 100, 2);
                    $data[$key]['合同未结算金额'] = round($unsettledAmount / 100, 2);
                    $data[$key]['已收票/已开票(金额）'] = round($outputInvoiceAmount / 100, 2);
                }

                unset($data[$key]['category']);
                unset($data[$key]['contract_type']);
                unset($data[$key]['contract_id']);
            }
        }
        $this->exportExcel($data);
    }
}