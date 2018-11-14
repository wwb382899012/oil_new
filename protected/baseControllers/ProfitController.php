<?php

/**
 * Desc: 利润分配
 * User: susiehuang
 * Date: 2017/11/24 0024
 * Time: 17:01
 */
class ProfitController extends Controller {
    public $category = 1; //统计类型
    public $indexView = '/profit/index';

    public function pageInit() {
        $this->filterActions = "";
        $this->rightCode = "profit";
    }

    public function actionIndex() {
        $attr = Mod::app()->request->getParam('search');
        $query = '';
        /*$category = $attr['category']; //统计类型
        unset($attr['category']);*/

        $type = $attr['type']; //分配类型
        unset($attr['type']);
        if (!empty($type)) {
            $query .= ' and a.type = ' . $type;
        }

        if (!empty($attr['startMonth'])) {
            list($startYear, $startMonth) = explode('-', $attr['startMonth']);
            unset($attr['startMonth']);
            $query .= ' and a.stat_year >= ' . $startYear . ' and a.stat_month >= ' . $startMonth;
        }
        if (!empty($attr['endMonth'])) {
            list($endYear, $endMonth) = explode('-', $attr['endMonth']);
            unset($attr['endMonth']);
            $query .= ' and a.stat_year <= ' . $endYear . ' and a.stat_month <= ' . $endMonth;
        }
        $leftTable = '';
        $title = '';
        $fields = 'a.type,concat(a.stat_year,"-",a.stat_month)as month,sum(a.buy_amount_invoice) as buy_amount_invoice,sum(a.buy_amount_settle) as buy_amount_settle,sum(a.buy_amount_paid) as buy_amount_paid,
                   sum(a.sell_amount_invoice) as sell_amount_invoice,sum(a.sell_amount_settle) as sell_amount_settle,sum(a.sell_amount_paid) as sell_amount_paid,
                   sum(b.factoring_interest) as factoring_interest,sum(b.factoring_fee) as factoring_fee,sum(b.factoring_fee2) as factoring_fee2,sum(b.amount_tax) as amount_tax,
                   sum(b.amount_custom) as amount_custom,sum(b.amount_stamp) as amount_stamp,sum(b.amount_surtax) as amount_surtax,sum(b.amount_store) as amount_store,
                   sum(b.amount_traffic) as amount_traffic,sum(b.amount_other) as amount_other,sum(b.amount_agent) as amount_agent,sum(b.amount_period) as amount_period,
                   sum(a.sell_amount_settle)-sum(a.buy_amount_settle) as cross_profit_amount, sum(b.factoring_interest)+sum(b.factoring_fee)+sum(b.factoring_fee2) as capital_cost,
                   sum(b.amount_tax)+sum(b.amount_custom)+sum(b.amount_stamp)+sum(b.amount_surtax) as tax_cost, sum(b.amount_store)+sum(b.amount_traffic)+sum(b.amount_other)+sum(b.amount_agent) as direct_cost';
        if ($this->category == ProjectProfit::CATEGORY_PROJECT) {
            $leftTable = ' left join t_project p on a.project_id = p.project_id ';
            $fields .= ',a.project_id,p.project_code,p.type as project_type';
            $query .= ' and a.project_id > 0 group by a.project_id order by a.project_id desc';
            $title .= '项目';
        } elseif ($this->category == ProjectProfit::CATEGORY_CORPORATION) {
            $leftTable = ' left join t_corporation c on c.corporation_id = a.corporation_id left join t_corporation co on co.corporation_id = c.corporation_id ';
            $fields .= ',a.corporation_id,co.name as corp_name';
            $query .= ' and a.corporation_id > 0 group by a.corporation_id order by a.corporation_id desc';
            $title .= '交易主体';
        } elseif ($this->category == ProjectProfit::CATEGORY_PROJECT_LEADER) {
            $leftTable = ' left join t_project p on a.project_id = p.project_id left join t_system_user su on su.user_id = p.manager_user_id';
            $fields .= ',p.manager_user_id,su.name as manager_name';
            $query .= ' and p.manager_user_id > 0 group by p.manager_user_id order by p.manager_user_id desc';
            $title .= '项目负责人';
        }

        if ($type == ProjectProfit::TYPE_CONFIRM) {
            $title .= '可分配';
        } elseif ($type == ProjectProfit::TYPE_SETTLED) {
            $title .= '可计算';
        }

        $sql1 = 'select ' . $fields . ' from t_project_profit a ' . $leftTable . ' 
                left join t_project_cost b on a.project_id = b.project_id and a.corporation_id = b.corporation_id and a.stat_year = b.stat_year and a.stat_month = b.stat_month ' . $this->getWhereSql($attr) . $query;

        $fields1 = '*, ss.cross_profit_amount-ss.capital_cost-ss.tax_cost-ss.direct_cost-ss.amount_period as net_profit_amount';
        $sql = 'select {fields} from (' . $sql1 . ') ss where 1=1';

        $dataProvider = new ZSqlDataProvider($sql, array('fields' => $fields1, 'pagination' => array('pageSize' => 20)));

//        $attr['category'] = isset($this->category) ? $this->category : '';
        $attr['type'] = isset($type) ? $type : '';
        if (isset($startYear) && isset($startMonth)) {
            $attr['startMonth'] = $startYear . '-' . $startMonth;
        }
        if (isset($endYear) && isset($endMonth)) {
            $attr['endMonth'] = $endYear . '-' . $endMonth;
        }
        $this->pageTitle = $title . '利润明细';
        $this->render($this->indexView, array('dataProvider' => $dataProvider, 'search' => $attr));
    }
}