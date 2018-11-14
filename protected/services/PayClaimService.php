<?php

/**
 * Desc: 付款认领
 */
class PayClaimService {
    public static function updatePaymentPlanAmount($plan_id, $amount) {
        $rows=PaymentPlan::model()->updateByPk($plan_id,
            array(
                "actual_amount"=>new CDbExpression("actual_amount+".$amount),
                "amount_paid"=>new CDbExpression("amount_paid+".$amount),
                "update_time"=>new CDbExpression("now()")
                )
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;

    }


    public static function updatePayApplicationAmount($pay, $amount) {
        $rows=PayApplication::model()->updateByPk($pay->apply_id,
            array(
                "amount_claim" =>new CDbExpression("amount_claim+".$amount),
                "update_time"=>new CDbExpression("now()"),
                ),"amount_paid-amount_claim>=:amount and status>=".PayApplication::STATUS_CHECKED,
                array('amount'=>$amount)
        );
        if($rows==1)
        {
            // $pay->amount_claim += $amount;
            PayService::doneClaimPayApplication($pay);
            return true;
        }
        else
            return false;

    }

    public static function getPayClaimDetail($claim_id) {
        $payClaim = PayClaim::model()->with('payClaimDetail', 'payClaimDetail.paymentPlan', 'contract')->findByPk($claim_id);
        if(empty($payClaim)) {
        	return array();
        }
        $payClaim = $payClaim->getAttributes(true, array("status_time", "create_user_id", "create_time", "update_user_id", "update_time"));
        if(!empty($payClaim->contract)) {
        	$payClaim['contract_code'] = $payClaim->contract->contract_code;
        	$payClaim['cont1ract_type'] = $payClaim->contract->type;
        }
        $payments = array();
        if(!empty($payClaim->contract_id)) {
        	$paymentPlans = PaymentPlan::model()->findAll(array('condition'=>'contract_id=:contract_id', 'params'=>array('contract_id'=>$payClaim->contract_id)));
        	$planMap = array();
	        foreach($payClaim->payClaimDetail as $detail) {
	        	$planMap[$detail->plan_id] = $detail->amount;
	        }
	        foreach($paymentPlans as $plan) {
	        	$paymentPlan = array();
	        	$paymentPlan['plan_id'] = $plan->plan_id;
	        	$paymentPlan['period'] = $plan->period;
	        	$paymentPlan['amount'] = $plan->amount;
	        	$paymentPlan['pay_date'] = $plan->pay_date;
	        	$paymentPlan['type'] = $plan->type;
	        	$paymentPlan['currency'] = $plan->currency;
	        	$paymentPlan['actual_amount'] = $plan->actual_amount;
	        	$paymentPlan['amount_input'] = isset($planMap[$plan->plan_id])?$planMap[$plan->plan_id]:0;
	        	$paymentPlan['check'] = isset($planMap[$plan->plan_id]);
	        	$payments[] = $paymentPlan;
	        } 	
        } 
        return array($payClaim, $payments);
    }

    /**
     * @desc 获取付款计划认领金额
     * @param int $planId
     * @return int
     */
    public static function getPaymentPlanClaimAmount($planId) {
        $planClaimAmount = 0;
        if(Utility::checkQueryId($planId) && $planId > 0) {
            $sql = 'select ifnull(sum(amount), 0) as total_amount from t_pay_claim_detail where plan_id='.$planId;
            $res = Utility::query($sql);
            if(Utility::isNotEmpty($res)) {
                $planClaimAmount = $res[0]['total_amount'];
            }
        }
        return $planClaimAmount;
    }

    /**
     * @desc 检查付款计划中认领金额是否合法
     * @param array $items
     * @return string|bool
     */
    public static function checkPaymentPlanPayClaimValid($items) {
        if (Utility::isNotEmpty($items)) {
            foreach ($items as $key => $row) {
                $required = array('plan_id', 'amount');
                if (!Utility::checkRequiredParamsNoFilterInject($row, $required)) {
                    return BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);
                }

                $plan = PaymentPlan::model()->findByPk($row['plan_id']);
                if(empty($plan)) {
                    return BusinessError::outputError(OilError::$PAYMENT_PLAN_NOT_EXIST, array('plan_id'=>$row['plan_id']));
                }
            }
        }
        return true;
    }

    /**
     * @desc 保存认领明细
     * @param array $items
     * @param int $claim_id
     * @param object $claim
     */
    public static function savePayClaimDetail($items, $claim_id, $claim = null) {
        if (Utility::isNotEmpty($items) && Utility::checkQueryId($claim_id) && $claim_id>0) {
            if(empty($claim)) {
                $claim = PayClaim::model()->findByPk($claim_id);
                if (empty($claim)) {
                    BusinessException::throw_exception(OilError::$PAY_CLAIM_NOT_EXIST, array('claim_id' => $claim_id));
                }
            }
            $data = PayClaimDetail::model()->findAll('claim_id='.$claim_id);
            $p = array();
            if (Utility::isNotEmpty($data)) {
                foreach ($data as $v) {
                    $p[$v["detail_id"]] = $v["detail_id"];
                }
            }

            foreach ($items as $row) {
                if (array_key_exists($row["detail_id"], $p)) {
                    $obj = PayClaimDetail::model()->findByPk($row["detail_id"]);
                    unset($p[$row["detail_id"]]);
                } else {
                    $obj = new PayClaimDetail();
                }
                unset($row['detail_id']);
                $plan = PaymentPlan::model()->findByPk($row['plan_id']);
                if(empty($plan)) {
                    BusinessException::throw_exception(OilError::$PAYMENT_PLAN_NOT_EXIST, array('plan_id' => $row['plan_id']));
                }
                $obj->apply_id = $claim->apply_id;
                $obj->claim_id = $claim_id;
                $obj->project_id = $plan->project_id;
                $obj->contract_id = $plan->contract_id;
                $obj->plan_id = $row['plan_id'];
                $obj->amount = $row['amount'];
                $obj->currency = $plan->currency;
                $obj->amount_cny = $obj->amount * $claim->exchange_rate;
                $obj->amount_paid = $row['amount'];
                $obj->amount_paid_cny = $row['amount'] * $claim->exchange_rate;
                $obj->status = PayClaimDetail::STATUS_SUBMITED;
                $obj->status_time = Utility::getDateTime();
                $obj->save();

                if ($claim->status == PayClaim::STATUS_SUBMITED) { //更新付款计划中实付金额
                    PaymentPlanService::updatePaidAmount($row['plan_id'], $row['amount']);
                }
            }
            if (count($p) > 0) {
                foreach ($p as $val) {
                    PayClaimDetail::model()->deleteByPk($val);
                }
            }
        } else {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }
    }
}