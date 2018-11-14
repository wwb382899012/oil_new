<?php
/**
 * @author 	vector
 * @date 	2018-06-11
 * @desc 	收付款占用利息父类	
 */

class InterestReportController extends Controller 
{
    public $type = ConstantMap::BUY_TYPE;
    
    public function actionIndex()
    {
        // $params = Mod::app()->request->getParam('search');
        $params = $this->getSearch();
        $user = Utility::getNowUser();

        $sql = 'select {col} from t_payment_interest p 
                left join t_payment_interest_change c on p.contract_id = c.contract_id ' . $this->getWhereSql($params) . ' and FIND_IN_SET(p.corporation_id , "'.$user['corp_ids'].'") and p.contract_type='.$this->type.' order by p.check_pass_time desc {limit}'; // and p.corporation_id in (10, 12) 

        $fields = '	p.id,p.corporation_id, p.corporation_name, p.project_id,p.project_code,
        			p.user_name,p.contract_id,p.contract_code,p.contract_type,p.amount_sign,p.stop_date,p.status as check_status,
        			case when p.status<'.PaymentInterest::STATUS_DONE.' then 0 else p.status end as status,c.amount_goods,c.amount_actual,c.days,c.interest';

        $data = $this->queryTablesByPage($sql, $fields);

        $data['contract_type'] = $this->type;
        $data["search"]=$params;

        $this->render('/paymentInterest/index', $data);
    }

    public static function checkIsCanEdit($status)
    {
    	if($status<=PaymentInterest::STATUS_NEW)
    		return true;
    	else
    		return false;
    }

    public function actionExport()
    {
        $params = Mod::app()->request->getParam('search');

        $user = Utility::getNowUser();

        if($this->type==ConstantMap::BUY_TYPE){
            $code_name    = "采购合同编号";
            $amount_desc  = "已入库货值(元)";
            $payment_desc = "累计实付金额(元)";
        }else{
            $code_name    = "销售合同编号";
            $amount_desc  = "已出库货值(元)";
            $payment_desc = "累计收款金额(元)";
        }

        $fields = ' p.corporation_name "交易主体", p.user_name 业务负责人,
                    p.project_code "项目编号", p.contract_code "'.$code_name.'", 
        			round(p.amount_sign/100, 2) "合同签约总额(元)",
        			round(c.amount_goods/100, 2) "'.$amount_desc.'",
        			round(c.amount_actual/100, 2) "'.$payment_desc.'",
        			c.days "计息天数",
        			round(c.interest/100 ,2) "合计利息(元)",
        			date_format(p.stop_date, "%Y/%c/%e") "停息日期",
        			case when p.status<'.PaymentInterest::STATUS_DONE.' then "正常"
        			when p.status='.PaymentInterest::STATUS_DONE.' then "已停息" end as "状态"';    

        $sql = 'select ' . $fields . ' from t_payment_interest p 
                left join t_payment_interest_change c on p.contract_id = c.contract_id ' . $this->getWhereSql($params) . ' and FIND_IN_SET(p.corporation_id , "'.$user['corp_ids'].'") and p.contract_type='.$this->type.' order by p.check_pass_time desc';//and p.corporation_id in (10, 12)

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }

    public function actionDetail()
    {
        $params = Mod::app()->request->getParam('search');
        if (!Utility::checkQueryId($params['contract_id']))
        {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $sql ='select p.id,p.corporation_id, p.corporation_name, p.project_id,p.project_code,
        		p.user_name, p.contract_id, p.contract_code, p.amount_sign,p.stop_date,
        		p.operator_name,p.stop_reason,p.status,c.amount_actual,c.days,c.interest 
        		from t_payment_interest p 
                left join t_payment_interest_change c on p.contract_id = c.contract_id 
                where p.contract_id='.$params['contract_id'].";";

        $payment = Utility::query($sql);
        
        $sql     = 'select {col} from t_payment_interest_detail  ' . $this->getWhereSql($params) . ' order by interest_date desc {limit}';
        
        $fields  = 'interest_date,amount_goods,amount_actual,amount_day,interest_day';
        
        $data    = $this->queryTablesByPage($sql, $fields);

        $data['payment'] = $payment[0];

        $this->pageTitle = $this->type == 1 ? '付款占用利息明细' : '收款占用利息明细';

		$data['search']['contract_id'] = $params['contract_id'];
        $data['search']['contract_type'] = $this->type;
        $this->render("/paymentInterest/detail", $data);
    }

    public function actionDetailExport()
    {
        $params = Mod::app()->request->getParam('search');
        if (!Utility::checkQueryId($params['contract_id']))
        {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        if($this->type==ConstantMap::BUY_TYPE){
            $amount_desc  = "已入库货值(元)";
            $payment_desc = "累计实付金额计(元)";
            $amount_day   = "当日实付金额(元)";
        }else{
            $amount_desc  = "已出库货值(元)";
            $payment_desc = "累计收款金额(元)";
            $amount_day   = "当日收款金额(元)";
        }


        $fields = 'date_format(interest_date, "%Y/%c/%e") "日期",
                   round(amount_goods/100, 2) "'.$amount_desc.'",
                   round(amount_actual/100, 2) "'.$payment_desc.'",
                   round(amount_day/100, 2) "'.$amount_day.'",
                   round(interest_day/100, 2) "日息(元)"';

        $sql = 'select ' . $fields . ' from t_payment_interest_detail ' . $this->getWhereSql($params) . ' order by interest_date desc';

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }


    public function actionStop()
    {
        $params=$_POST["obj"];
        if(!Utility::checkQueryId($params['id']))
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        if(empty($params['stop_date']))
            $this->returnError(BusinessError::outputError(OilError::$STOP_DATE_IS_NOT_NULL));
        if(empty($params['stop_reason']))
            $this->returnError(BusinessError::outputError(OilError::$STOP_REASON_IS_NOT_NULL));

        $obj = PaymentInterest::model()->findByPk($params['id']);
        if(empty($obj->id))
            $this->returnError(BusinessError::outputError(OilError::$PAYMENT_INTEREST_NOT_EXIST, array('contract_code' => $params['contract_code'])));

        $obj->operator_id    = Utility::getNowUserId();
        $obj->operator_name  = SystemUser::getNameById($obj->operator_id);
        $obj->update_time    = Utility::getDateTime();
        $obj->update_user_id = Utility::getNowUserId();
        $obj->stop_reason    = $params['stop_reason'];
        $obj->stop_date      = $params['stop_date'];

        $nowtime  = strtotime(Utility::getDate());
        $stoptime = strtotime($params['stop_date']);

        $status = PaymentInterest::STATUS_DONE;
        if($nowtime <= $stoptime)
            $status = PaymentInterest::STATUS_PASS;

        $obj->status = $status;

        $trans = Utility::beginTransaction();
        try {
            // print_r($obj);die;
            $obj->save();

            if($nowtime > $stoptime)
                InterestReportService::addDayInterestByContractId($obj->contract_id);

            $trans->commit();
            $this->returnSuccess();
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$STOP_INTEREST_ERROR, array('reason' => $e->getMessage())));
        }
    }


    public function actionCreateData()
    {
        echo "======开始生成利息明细相关数据======<br/>";
        InterestReportService::addInterestInfo();
        sleep(3);
        InterestReportService::addDayInterest();
        echo "======成功生成利息明细相关数据======<br/>";
    }
    
}