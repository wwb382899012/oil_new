<?php

/**
 * Desc: 风控额度预警
 * User: wwb
 * Date: 2018/3/22 0022
 * Time: 16:42
 */
class RiskAmountWarningController extends Controller
{
    public function pageInit()
    {
        $this->rightCode = 'RiskAmountWarning';
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

        $sql = 'select {col} from t_partner_amount_warning a
                left join t_partner b on a.partner_id = b.partner_id ' . $this->getWhereSql($params) . ' order by a.join_time desc {limit}';

        $fields = 'a.*,b.name as partner_name';
        $data = $this->queryTablesByPage($sql, $fields);

        $this->render("index", $data);
    }

    public function actionExport()
    {
        $params = Mod::app()->request->getParam('search');

        $fields = "b.name 合作方名称,
                   case when a.status=0 then '正常' else '催收' end 状态,
                   DATE_FORMAT(a.join_time,'%Y/%m/%d') 合作日期,
                   case when a.level=1 then 'A类' when a.level=2 then 'B类' when a.level=3 then'C类' else 'D类' end as 评级,
                   concat(round(ifnull(a.credit_amount,0)/1000000, 2),'万元') 初始额度,
                   concat(round(ifnull(a.change_amount,0)/1000000, 2),'万元') 变动额度,
                   a.change_reason 变动原因,
                   concat(round(ifnull(a.credit_total_amount,0)/1000000, 2),'万元') 信用总额度,
                   concat(round(ifnull(a.actual_used_amount,0)/1000000, 2),'万元') 额度占用,
                   concat(round(ifnull(a.available_amount,0)/1000000, 2),'万元') 可用额度,
                   a.over_nums 历史逾期次数,
                   concat(a.max_over_days,'天') 最长逾期天数

                  ";

        $sql = 'select ' . $fields . ' from t_partner_amount_warning a
                left join t_partner b on a.partner_id = b.partner_id ' . $this->getWhereSql($params) . ' order by a.join_time desc';

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }

    /**
     * 生成数据
     * */
    public function actionAdd(){
        $riskAmount = new ReportCommand(null,null);
        $riskAmount->actionRiskAmountWarning();
    }
}