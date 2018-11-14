<?php
/**
 * Created by vector.
 * DateTime: 2017/10/10 15:38
 * Describe：
 */

class InvoiceService
{
	public static function getAllContract()
	{
		$sql = "select c.corporation_id,c.contract_id,c.contract_code,c.type,
				p.project_id,p.project_code
				from t_contract c
				left join t_project p on c.project_id=p.project_id 
				where c.contract_code is not null order by contract_id asc";
		$data= Utility::query($sql);
		return $data;
	}

	public static function getAllProject()
	{
		$sql = "select p.corporation_id,c.contract_id,c.contract_code,c.type,p.project_id,p.project_code
				from t_project p
				left join t_contract c on p.project_id=c.project_id order by project_id asc";
		$data= Utility::query($sql);
		return $data;
	}


	public static function getUniqueProject()
	{
		$sql = "select p.corporation_id,p.project_id,p.project_code
				from t_project p order by project_id asc";
		$data= Utility::query($sql);
		return $data;
	}


	//保存发票明细
    public static function saveInvoiceApplyDetail($invoiceItems, $applyId, $isSave, $typeSub) {
        if(Utility::isEmpty($invoiceItems) || !Utility::checkQueryId($applyId) || empty($typeSub))
            return;

        $sql    = "select detail_id from t_invoice_application_detail where apply_id=" . $applyId;
        $data   = Utility::query($sql);
        $p      = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v['detail_id'];
            }
        }
        // print_r($goodsItems);die;
        if(Utility::isNotEmpty($invoiceItems)){
            foreach ($invoiceItems as $row) {
                if (array_key_exists($row["detail_id"], $p)) {
                    $invoiceDetail = InvoiceApplicationDetail::model()->findByPk($row["detail_id"]);
                    if (empty($invoiceDetail->detail_id)) {
                        unset($p[$row["detail_id"]]);
                        return;
                    }
                } else {
                    $invoiceDetail = new InvoiceApplicationDetail();
                }
                $invoiceDetail->apply_id      = $applyId;
                if($typeSub==ConstantMap::PAYMENT_GOODS_TYPE){
                	$invoiceDetail->goods_id  = $row['goods_id'];
                	$invoiceDetail->quantity  = $row['quantity'];
                	$invoiceDetail->price     = $row['price'];
                	$invoiceDetail->unit  	  = $row['unit'];
                }else{
                	$invoiceDetail->invoice_name = $row['invoice_name'];
                }
                /*$invoiceDetail->contract_id   = $row["contract_id"];
                $invoiceDetail->project_id    = $row["project_id"];*/
                $invoiceDetail->amount     	  = $row['amount'];
                $invoiceDetail->rate_type     = $row['rate_type'];
                if($row['rate_type']!=ConstantMap::INVOICE_RATE_TYPE){
                    $invoiceDetail->rate      = Map::$v['goods_invoice_rate'][$row['rate_type']]['value'];
                }else{
                    $invoiceDetail->rate      = $row['rate'];
                }

                $invoiceDetail->status        = 1;
                $invoiceDetail->status_time   = new CDbExpression("now()");
                $invoiceDetail->create_time   = new CDbExpression("now()");
                $invoiceDetail->create_user_id= Utility::getNowUserId();
                $invoiceDetail->update_time   = new CDbExpression("now()");
                $invoiceDetail->update_user_id= Utility::getNowUserId();
                $invoiceDetail->save();

                unset($p[$row["detail_id"]]);
            }
        }

        if (count($p) > 0) {
            InvoiceApplicationDetail::model()->deleteAll('detail_id in(' . implode(',', $p) . ')');
        }
    }

    //保存付款计划明细
    public static function savePaymentDetail($paymentItems, $applyId, $isSave) {
        if(Utility::isEmpty($paymentItems) || !Utility::checkQueryId($applyId))
            return;

        $sql    = "select detail_id from t_invoice_pay_plan where apply_id=" . $applyId;
        $data   = Utility::query($sql);
        $p      = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v['detail_id'];
            }
        }
        // print_r($goodsItems);die;
        if(Utility::isNotEmpty($paymentItems)){
            foreach ($paymentItems as $row) {
                if(empty($row))
                    continue;
                if (array_key_exists($row["detail_id"], $p)) {
                    $payPlan = InvoicePayPlan::model()->findByPk($row["detail_id"]);
                    if (empty($payPlan->detail_id)) {
                        unset($p[$row["detail_id"]]);
                        return;
                    }
                } else {
                    $payPlan = new InvoicePayPlan();
                }
                $payPlan->apply_id      = $applyId;
                $payPlan->plan_id   	= $row["plan_id"];
                $payPlan->contract_id   = $row["contract_id"];
                $payPlan->project_id    = $row["project_id"];
                $payPlan->amount      	= $row['amount'];
                $payPlan->status        = 1;
                $payPlan->status_time   = new CDbExpression("now()");
                $payPlan->create_time   = new CDbExpression("now()");
                $payPlan->create_user_id= Utility::getNowUserId();
                $payPlan->update_time   = new CDbExpression("now()");
                $payPlan->update_user_id= Utility::getNowUserId();

                $payPlan->save();
                unset($p[$row["detail_id"]]);
            }
        }
        if (count($p) > 0) {
            InvoicePayPlan::model()->deleteAll('detail_id in(' . implode(',', $p) . ')');
        }
    }

    //获取所有付款计划并在当前发票申请下的
    public static function getPayment($contractId, $applyId, $type)
    {
    	$data = array();
    	if(!Utility::checkQueryId($contractId) || !Utility::checkQueryId($applyId))
            return $data;

        $sql = "select p.detail_id,a.plan_id,a.project_id,a.contract_id,a.pay_date,a.expense_type,a.expense_name,
                a.amount as pay_amount,a.currency,a.amount_invoice,IFNULL(p.amount, 0) as amount
                from t_payment_plan a
                left join t_invoice_pay_plan p on p.plan_id=a.plan_id and p.apply_id=".$applyId."
                where a.contract_id=".$contractId." and a.type=".$type." order by a.plan_id asc";
        $data= Utility::query($sql);
        if(Utility::isNotEmpty($data)){
        	$map = Map::$v;
        	foreach ($data as $key => $value) {
        		$data[$key]['currency_desc']    = $map['currency'][$value['currency']]['name'];
                $data[$key]['currency_ico']     = $map['currency'][$value['currency']]['ico'];
                $data[$key]['expense_desc']     = $value['expense_type']!=5?$map['pay_type'][$value['expense_type']]['name']:$map['pay_type'][$value['expense_type']]['name'].'--'.$value['expense_name'];
        	}
        }
        return $data;
    }

    public static function getInvoiceApplyDetail($applyId)
    {
    	$data = array();
    	if(!Utility::checkQueryId($applyId))
            return $data;

        $sql = "select d.*, a.type_sub, g.name as goods_name
        		from t_invoice_application a
                left join t_invoice_application_detail d on a.apply_id=d.apply_id
                left join t_goods g on d.goods_id=g.goods_id
                where d.apply_id=".$applyId." order by d.detail_id asc";
        $data    = Utility::query($sql);
        return $data;
    }

    //获取当前申请的付款计划
    public static function getPaymentById($applyId, $type)
    {
        $data = array();
        if(!Utility::checkQueryId($applyId) || !Utility::checkQueryId($type))
            return $data;

        $sql = "select p.detail_id,a.plan_id,a.project_id,a.contract_id,a.pay_date,a.expense_type,expense_name,
                a.amount as pay_amount,a.currency,a.amount_invoice,IFNULL(p.amount, 0) as amount
                from t_invoice_pay_plan p 
                left join t_payment_plan a on p.plan_id=a.plan_id
                where p.apply_id=".$applyId." and a.type=".$type." order by a.plan_id asc";
        $data= Utility::query($sql);
        if(Utility::isNotEmpty($data)){
            $map = Map::$v;
            foreach ($data as $key => $value) {
                $data[$key]['currency_desc']    = $map['currency'][$value['currency']]['name'];
                $data[$key]['currency_ico']     = $map['currency'][$value['currency']]['ico'];
                $data[$key]['expense_desc']     = $value['expense_type']!=5?$map['pay_type'][$value['expense_type']]['name']:$map['pay_type'][$value['expense_type']]['name'].'--'.$value['expense_name'];
            }
        }
        return $data;
    }

    //获取当前申请的所有开票信息
    public static function getAllInvoiceInfo($applyId)
    {
        $data = array();
        if(empty($applyId))
            return $data;
        $data = Invoice::model()->findAllToArray(array("condition"=>"apply_id=".$applyId, "order"=>"invoice_id desc"));

        return $data;
    }

    public static function getLastInvoiceInfo($applyId)
    {
        $data = array();
        if(empty($applyId))
            return $data;
        $sql = "select * from t_invoice where apply_id=".$applyId." order by invoice_id desc limit 1";

        $data = Utility::query($sql);
        return $data;
    }

    //保存开票明细
    public static function saveInvoiceDetail($invoiceItems, $invoiceId, $isSave, $typeSub) {
        if(Utility::isEmpty($invoiceItems) || !Utility::checkQueryId($invoiceId) || empty($typeSub))
            return;

        $sql    = "select detail_id from t_invoice_detail where invoice_id=" . $invoiceId;
        $data   = Utility::query($sql);
        $p      = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v['detail_id'];
            }
        }
        // print_r($goodsItems);die;
        if(Utility::isNotEmpty($invoiceItems)){
            foreach ($invoiceItems as $row) {
                if (array_key_exists($row["detail_id"], $p)) {
                    $invoiceDetail = InvoiceDetail::model()->findByPk($row["detail_id"]);
                    if (empty($invoiceDetail->detail_id)) {
                        unset($p[$row["detail_id"]]);
                        return;
                    }
                } else {
                    $invoiceDetail = new InvoiceDetail();
                }
                $invoiceDetail->invoice_id      = $invoiceId;
                if($typeSub==ConstantMap::PAYMENT_GOODS_TYPE){
                    $invoiceDetail->goods_id  = $row['goods_id'];
                    $invoiceDetail->quantity  = $row['quantity'];
                    $invoiceDetail->unit      = $row['unit'];
                    $invoiceDetail->price     = $row['price'];
                }else{
                    $invoiceDetail->invoice_name = $row['invoice_name'];
                }
                /*$invoiceDetail->contract_id   = $row["contract_id"];
                $invoiceDetail->project_id    = $row["project_id"];*/
                $invoiceDetail->rate          = $row['rate'];
                $invoiceDetail->amount        = $row['amount'];
                $invoiceDetail->invoice_date  = $row['invoice_date'];
                $invoiceDetail->status        = 1;
                $invoiceDetail->status_time   = new CDbExpression("now()");
                $invoiceDetail->create_time   = new CDbExpression("now()");
                $invoiceDetail->create_user_id= Utility::getNowUserId();
                $invoiceDetail->update_time   = new CDbExpression("now()");
                $invoiceDetail->update_user_id= Utility::getNowUserId();
                $invoiceDetail->save();

                unset($p[$row["detail_id"]]);
            }
        }

        if (count($p) > 0) {
            InvoiceDetail::model()->deleteAll('detail_id in(' . implode(',', $p) . ')');
        }
    }


    //获取当前申请的所有开票详细信息
    public static function getAllInvoiceDetail($applyId)
    {
        $data = array();
        if(empty($applyId))
            return $data;
        $sql = "select d.*,i.invoice_id,i.invoice_num,i.remark,i.status,a.type_sub,g.name as goods_name
                from t_invoice i 
                left join t_invoice_detail d on i.invoice_id=d.invoice_id 
                left join t_invoice_application a on i.apply_id=a.apply_id
                left join t_goods g on d.goods_id=g.goods_id
                where i.apply_id=".$applyId." order by i.invoice_id desc,d.detail_id asc ";
        $data=  Utility::query($sql);
        return $data;
    }


    //获取合同下的品名
    public static function getContractGoods($id, $type)
    {
        $data = array();
        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($type))
            return $data;
        
        $sql = "select g.goods_id, g.name,c.unit
                from t_contract_goods c 
                left join t_goods g on c.goods_id=g.goods_id
                where c.contract_id=".$id." and c.type=".$type." group by g.goods_id order by c.detail_id asc";
        $data= Utility::query($sql);
        return $data;
    }

}