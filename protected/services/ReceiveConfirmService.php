<?php

/**
 * Desc: 流水认领
 */
class ReceiveConfirmService
{

    /**
     * @desc 获取附件信息
     * @param $id
     * @param $type
     * @return array
     */
    public static function getAttachment($id, $type = '') {
        if (empty($id)) {
            return array();
        }
        if (!empty($type)) {
            $type = ' and type=' . $type;
        }

        $sql = "select * from t_receive_confirm_file_temp_attachment where base_id=" . $id . " and status=1" . $type . " order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }
        return $attachments;
    }


    public static function updatePaymentPlanAmount($plan_id, $amount) {
        $rows = PaymentPlan::model()->updateByPk($plan_id,
            array(
                "actual_amount" => new CDbExpression("actual_amount+" . $amount),
                "amount_paid" => new CDbExpression("amount_paid+" . $amount),
                "update_time" => new CDbExpression("now()")
            )
        );
        if ($rows == 1) {
            return true;
        } else
            return false;

    }


    public static function updateBankFlowAmount($flow_id, $amount) {
        $rows = BankFlow::model()->updateByPk($flow_id,
            array(
                "amount_claim" => new CDbExpression("amount_claim+" . $amount),
                "update_time" => new CDbExpression("now()")
            ), "amount-amount_claim>=:amount",
            array('amount' => $amount)
        );
        $updateStatus = BankFlow::model()->updateByPk($flow_id, array('status' => BankFlow::STATUS_DONE), 'amount=amount_claim');
        if ($rows == 1) {
            return true;
        } else
            return false;

    }

    public static function getReceiveConfirmDetail($receive_id) {
        $receiveConfirm = ReceiveConfirm::model()->findByPk($receive_id);
        if (empty($receiveConfirm)) {
            return array();
        }
        $receiveConfirmDetail = $receiveConfirm->getAttributes(true, array("status_time", "create_user_id", "create_time", "update_user_id", "update_time"));
        $receiveConfirmDetail['creator'] = $receiveConfirm->creator->name;
        if (!empty($receiveConfirm->contract)) {
            $receiveConfirmDetail['contract_code'] = $receiveConfirm->contract->contract_code;
            $receiveConfirmDetail['contract_type'] = $receiveConfirm->contract->type;
        }
        if (!empty($receiveConfirm->project)) {
            $receiveConfirmDetail['project_code'] = $receiveConfirm->project->project_code;
            $receiveConfirmDetail['project_type'] = $receiveConfirm->project->type;
        }
        $payments = array();
        if (!empty($receiveConfirm->contract_id)) {
            $paymentPlans = PaymentPlan::model()->findAll(array('condition' => 'contract_id=:contract_id', 'params' => array('contract_id' => $receiveConfirm->contract_id)));
            $planMap = array();
            foreach ($receiveConfirm->receiveDetail as $detail) {
                $planMap[$detail->plan_id] = $detail->amount;
            }
            foreach ($paymentPlans as $plan) {
                $paymentPlan = array();
                $paymentPlan['plan_id'] = $plan->plan_id;
                $paymentPlan['period'] = $plan->period;
                $paymentPlan['amount'] = $plan->amount;
                $paymentPlan['pay_date'] = $plan->pay_date;
                $paymentPlan['type'] = $plan->type;
                $paymentPlan['currency'] = $plan->currency;
                $paymentPlan['amount_paid'] = $plan->amount_paid;
                $paymentPlan['amount_input'] = isset($planMap[$plan->plan_id]) ? $planMap[$plan->plan_id] : 0;
                $paymentPlan['check'] = isset($planMap[$plan->plan_id]);
                $payments[] = $paymentPlan;
            }
        }
        return array($receiveConfirmDetail, $payments);
    }

    /**
     * @desc 根据合同id获取已收款金额
     * @param int $contractId
     * @return int;
     */
    public static function getReceivedGoodsAmountByContractId($contractId) {
        $res = 0;
        if (Utility::checkQueryId($contractId)) {
            $sql = 'select ifnull(sum(amount),0) as total_amount from t_receive_confirm where contract_id = ' . $contractId . ' and status>=1 and subject in (' . ConstantMap::GOODS_FEE_SUBJECT_ID . ')';
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                $taxAmount = ContractService::getContractTaxActualReceivedAmount($contractId);
                $res = $data[0]['total_amount'] - $taxAmount;
            }
        }
        return $res;
    }

    /**
     * @desc 根据合同ID获取已经认领的金额
     * @param $contractId
     * @param string $subject_list
     * @return mixe
     *
     */
    public static function getReceivedAmountByContractId($contractId, $subject_list = '') {
        $res = 0;
        $receive_sql = "
                select ifnull(sum(amount),0) as total_amount from t_receive_confirm a
                where a.contract_id = " . $contractId . "  and a.status >= " . ReceiveConfirm::STATUS_SUBMITED . "
               ";
        if ($subject_list) {
            $receive_sql .= "and a.subject in (" . $subject_list . ")";
        }
        $data = Utility::query($receive_sql);
        if (Utility::isNotEmpty($data)) {
            $res = $data[0]['total_amount'];
        }
        return $res;
    }
}