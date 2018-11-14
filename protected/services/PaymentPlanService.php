<?php

/**
 * Desc: 子合同收付款计划服务
 * User: susiehuang
 * Date: 2017/8/30 0031
 * Time: 11:05
 */
class PaymentPlanService {
    /**
     * @desc 检查收付款计划参数是否合法
     * @param array $plans
     * @param int $amount
     * @param int $type
     * @return bool|string
     */
    public static function checkParamsValid($plans, $amount, $type = 1) {
        $res = array('code' => 0, 'error_msg' => '');
        if (Utility::isNotEmpty($plans)) {
            $flag = false;
            $totalAmount = 0;
            foreach ($plans as $key => $row) {
                if (!empty($row['expense_type'])) {
                    $flag = true;
                    $requiredParams = array('pay_date', 'currency');
                    if ($row['type'] == ConstantMap::RECEIVE_PAY_TYPE_OTHER) {
                        array_push($requiredParams, 'expense_name');
                    }
                    $label = '上游付款计划';
                    if ($type == ConstantMap::CONTRACT_CATEGORY_SUB_SALE) {
                        $label = '下游收款计划';
                    }/*else{
                        array_push($requiredParams, 'payment_term');
                    }*/
                    //必填参数校验
                    if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                        $res['code'] = - 1;
                        $res['error_msg'] = BusinessError::outputError(OilError::$PAYMENT_PLAN_PARAMS_CHECK_ERROR, array('label' => $label));
                    }

                    $totalAmount += $row['amount'];
                }
            }
            /*if ($flag) {
                if ($totalAmount > $amount) {
                    $res['code'] = - 2;
                    $res['error_msg'] = BusinessError::outputError(OilError::$TOTAL_PAYMENT_AMOUNT_GR_BUY_AMOUNT);
                }
            }*/
        }

        return $res;
    }

    /**
     * @desc 保存收付款计划
     * @param array $plans
     * @param int $project_id
     * @param int $contract_id
     * @return array|int
     */
    public static function savePaymentPlanItems($plans, $project_id, $contract_id) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($plans) . ' project_id is:' . $project_id . ', contract_id is:' . $contract_id);
        if (Utility::isEmpty($plans) || empty($project_id) || empty($contract_id)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $data = PaymentPlan::model()->findAll('project_id = :projectId and contract_id = :contractId', array('projectId' => $project_id, 'contractId' => $contract_id));
        $p = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["plan_id"]] = $v["plan_id"];
            }
        }

        foreach ($plans as $key => $row) {
            if (empty($row['expense_type']) && empty($row["expense_name"])) {
                continue;
            }
            if (array_key_exists($row["plan_id"], $p)) {
                $obj = PaymentPlan::model()->findByPk($row["plan_id"]);
                unset($p[$row["plan_id"]]);
            } else {
                $obj = new PaymentPlan();
                $obj->contract_id = $contract_id;
                $obj->project_id = $project_id;
                $obj->period = $key + 1;
                $obj->status = ConstantMap::STATUS_NEW;
            }
            unset($row['plan_id']);
            $obj->setAttributes($row, false);
            $obj->save();
        }
        if (count($p) > 0) {
            foreach ($p as $val) {
                PaymentPlan::model()->deleteByPk($val);
            }
        }
    }

    /**
     * @desc 格式化前端展示收付款信息
     * @param array $payments
     * @return array
     */
    public static function reversePaymentPlans($payments) {
        $res = array();
        if (Utility::isNotEmpty($payments)) {
            foreach ($payments as $key => $row) {
                $res[$key]['plan_id'] = $row['plan_id'];
                $res[$key]['pay_date'] = $row['pay_date'];
                $res[$key]['expense_type'] = $row['expense_type'];
                $res[$key]['amount'] = $row['amount'];
                $res[$key]['currency'] = $row['currency'];
                $res[$key]['expense_name'] = $row['expense_name'];
                $res[$key]['type'] = $row['type'];
                // $res[$key]['payment_term'] = $row['payment_term'];
                $res[$key]['remark'] = $row['remark'];
            }
        }

        return $res;
    }

    /**
     * 更新付款计划的已付金额（对应付款申请的金额）
     * @param $planId
     * @param $amount
     * @return bool
     */
    public static function updatePaidAmount($planId,$amount)
    {
        if(empty($planId))
            return false;
        $rows=PaymentPlan::model()->updateByPk($planId,array(
            "amount_paid"=>new CDbExpression("amount_paid+".$amount),
            "update_time"=>new CDbExpression("now()"),
            "update_user_id"=>Utility::getNowUserId()
        ));
        return $rows==1;
    }

    /**
     * 更新付款计划的实际付款金额
     * @param $planId
     * @param $amount
     * @return bool
     */
    public static function updateActualPaidAmount($planId,$amount)
    {
        if(empty($planId))
            return false;
        $rows=PaymentPlan::model()->updateByPk($planId,array(
            "actual_amount"=>new CDbExpression("actual_amount+".$amount),
            "update_time"=>new CDbExpression("now()"),
            "update_user_id"=>Utility::getNowUserId()
        ));
        return $rows==1;
    }

}
