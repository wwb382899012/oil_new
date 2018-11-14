<?php
/**
 * @author 	vector
 * @date 	2018-06-25
 * @desc 	项目资金占用报表	
 */

class FundOccupyController extends Controller 
{
    public function pageInit() {
        $this->filterActions = "export";
        $this->rightCode = "fundOccupy";
        $this->newUIPrefix = 'new_';
    }
    
    public function actionIndex()
    {
        // $params = Mod::app()->request->getParam('search');
        $params = $this->getSearch();

        $user = Utility::getNowUser();

        $columns= ' i.corporation_id,i.corporation_name,i.project_id,i.project_code, i.user_name,
                    (SELECT IFNULL(sum(ic.amount_actual), 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=1) as amount_pay,
                    (SELECT IFNULL(sum(ic.amount_actual), 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=2) as amount_receive,
                    (SELECT IFNULL(sum(ic.interest), 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=1) as interest_pay,
                    (SELECT IFNULL(sum(ic.interest), 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=2) as interest_receive';

        $sql = 'select {col}
                from ( select '.$columns.' from t_payment_interest i ' . $this->getWhereSql($params) . ' and FIND_IN_SET(i.corporation_id , "'.$user['corp_ids'].'") group by i.project_id order by i.project_id desc) temp {limit} '; // and i.corporation_id in (10, 12)

        $fields = "corporation_id,corporation_name,project_id,project_code,user_name,
                amount_pay,amount_receive,interest_pay,interest_receive";

        $data = $this->queryTablesByPage($sql, $fields);
        $data["search"]=$params;

        $this->render('index', $data);
    }

    public function actionExport()
    {
        $params = Mod::app()->request->getParam('search');

        $user = Utility::getNowUser();

        $fields = ' i.corporation_name "交易主体",i.user_name 业务负责人,i.project_code "项目编号",
                    (SELECT IFNULL(sum(ic.amount_actual)/100, 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=2) as "累计收款金额(元)",
                    (SELECT IFNULL(sum(ic.amount_actual)/100, 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=1) as "累计实付金额(元)",
                    (SELECT IFNULL(sum(ic.interest)/100, 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=1) as "累计实付利息(元)",
                    (SELECT IFNULL(sum(ic.interest)/100, 0) from t_payment_interest pi 
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id 
                    where pi.project_id=i.project_id and pi.contract_type=2) as "累计收款利息(元)"';    

        $sql = 'select ' . $fields . ' from t_payment_interest i ' . $this->getWhereSql($params) . ' and FIND_IN_SET(i.corporation_id , "'.$user['corp_ids'].'") group by i.project_id order by i.project_id desc'; // and i.corporation_id in (10, 12)

        $data = Utility::query($sql);

        if(!empty($data)){
            foreach ($data as $k=>$v) {
                $data[$k]['合计利息(元)'] = $v['累计实付利息(元)'] - $v['累计收款利息(元)'];
            }
        }
        $this->exportExcel($data);
    }
    
}