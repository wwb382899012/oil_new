<?php

/**
 * Desc: 下游客户报表
 * User: wwb
 * Date: 2018/6/14 0022
 * Time: 16:42
 */
class PartnerSellContractController extends Controller
{
    public function pageInit()
    {
        $this->rightCode = 'PartnerSellContract';
        $this->filterActions = "index,add,export";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex()
    {
        $params = Mod::app()->request->getParam('search');
        if(!empty($params['a.join_time<'])){
            $end_time = $params['a.join_time<'];
            $params['a.join_time<']=$end_time." 23:59:59";
        }

        $sql = 'select {col} from t_partner_sell_contract a
                left join t_partner b on a.partner_id = b.partner_id ' . $this->getWhereSql($params) . ' order by a.join_time desc {limit}';

        $fields = 'a.*,b.name as partner_name';
        $data = $this->queryTablesByPage($sql, $fields);

        $this->render("index", $data);
    }

    public function actionExport()
    {
        $params = Mod::app()->request->getParam('search');

        $fields = "b.name 合作方名称,
                   concat(round(ifnull(a.contract_amount,0)/100, 2),'元') 签约金额,
                   concat(round(ifnull(a.delivery_amount,0)/100, 2),'元') 已提货货值,
                   concat(round(ifnull(a.not_delivery_amount,0)/100, 2),'元') 未提货货值,
                   concat(a.delivery_quantity,'吨') 已提货数量,
                   concat(a.not_delivery_quantity,'吨') 未提货数量,
                   concat(round(ifnull(a.receive_amount,0)/100, 2),'元') 已收款金额,
                   concat(round(ifnull(a.not_receive_amount,0)/100, 2),'元') 未收款金额,
                   concat(a.invoice_quantity,'吨') 已开票数量,
                   a.invoice_max_overdue_time 最长超期开票时间,
                   concat(a.not_invoice_quantity_delivery,'吨') 未开票数量（按交货）,
                   concat(round(ifnull(a.not_invoice_amount_delivery,0)/100, 2),'元') 未开票金额（按交货）,
                   concat(a.not_invoice_quantity_contract,'吨') 未开票数量（按合同）,
                   concat(round(ifnull(a.not_invoice_amount_contract,0)/100, 2),'元') 未开票金额（按合同）,
                   concat(a.settle_quantity,'吨') 已结算数量,
                   concat(round(ifnull(a.settle_amount,0)/100, 2),'元') 已结算人民币金额（货款）
                  ";

        $sql = 'select ' . $fields . ' from t_partner_sell_contract a
                left join t_partner b on a.partner_id = b.partner_id ' . $this->getWhereSql($params) . ' order by a.join_time desc';

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }

    /**
     * 生成数据
     * */
    public function actionAdd(){
        $riskAmount = new ReportCommand(null,null);
        $riskAmount->actionPartnerSellContract();
    }
}