<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/24 15:21
 * Describe：
 */

class FactoringService
{

    /**
     * 发起保理申请
     * @param ActiveRecord $payApplication
     * @return bool
     */
    public static function applyFactoring($payApplication)
    {
        $obj=Factor::model()->find("apply_id=".$payApplication->apply_id);
        if(empty($obj))
        {
            $obj=new Factor();
            $obj->apply_id=$payApplication->apply_id;
            $codeInfo = CodeService::getFactoringCode($payApplication->corporation_id);
            if($codeInfo['code'] == ConstantMap::INVALID) {
                return false;
            }
            $obj->contract_code = $codeInfo['data'];
            $obj->contract_code_fund = CodeService::getFactoringFundCode();
        }

        $obj->apply_amount=$payApplication->amount_factoring;
        $obj->contract_id=$payApplication->contract_id;
        $obj->project_id=$payApplication->project_id;
        $obj->corporation_id=$payApplication->corporation_id;
        $res=$obj->save();
        if($res === true) {
            $factorFundCode = FactorFundCode::model()->findByCode($obj->contract_code_fund);
            if(empty($factorFundCode)) {
                $factorFundCode = new FactorFundCode();
                $factorFundCode->code = $obj->contract_code_fund;
                $factorFundCode->type = FactorFundCode::TYPE_INTERNAL;
            }
            $res1 = $factorFundCode->save();
            return $res1;
        }
        return $res;
    }

    /**
     * @desc 根据付款申请id更新保理单状态
     * @param int $applyId
     * @throws
     */
    public static function updateFactorStatusByPayApply($applyId) {
        if (Utility::checkQueryId($applyId)) {
            $factor = Factor::model()->find('apply_id = :applyId', array('applyId' => $applyId));
            if(!empty($factor) && $factor->status < Factor::STATUS_SUBMIT)
            {
                $rows = $factor->updateByPk($factor->factor_id,array(
                    "status"=>Factor::STATUS_SUBMIT,
                    "status_time"=>new CDbExpression("now()"),
                    "update_time"=>new CDbExpression("now()"),
                    "update_user_id"=>Utility::getNowUserId()
                ),"status<".Factor::STATUS_SUBMIT);

                if($rows != 1)
                    throw new Exception("更新保理单状态失败！");
            }
        }
    }

    /**
     * @desc 检查是否财务会计确认
     * @param int $status
     * @return bool
     */
    public static function checkIsCanConfirm($status) {
        return in_array($status, Factor::$canConfirmStatus);
    }

    /**
     * @desc 获取对接列表保理状态
     * @param int $factorId
     * @return int
     */
    /*public static function getFactorStatus($factorId) {
        $status = - 999;
        if (Utility::checkQueryId($factorId)) {
            $factorModel = Factor::model()->findByPk($factorId);
            if (!empty($factorModel->factor_id)) {
                if ($factorModel->status == Factor::STATUS_CONFIRM) { //审批通过
                    $sql = 'select detail_id, role_id from t_check_detail where obj_id = ' . $factorId . ' and business_id = ' . ConstantMap::FACTOR_BUSINESS_ID . ' and status = 0 order by detail_id desc limit 1';
                    $data = Utility::query($sql);
                    if (Utility::isNotEmpty($data)) {
                        switch ($data[0]['role_id']) {
                            case ConstantMap::BOARD_FINANCE_LEADER_ROLE_ID:
                                $status = 5;
                                break;
                            case ConstantMap::FINANCE_LEADER_ROLE_ID:
                                $status = 7;
                                break;
                            case ConstantMap::CASHIER_ROLE_ID:
                                $status = 9;
                                break;
                        }
                    }
                } elseif ($factorModel->status == Factor::STATUS_BACK) {
                    $sql = 'select detail_id, role_id from t_check_detail where obj_id = ' . $factorId . ' and business_id = ' . ConstantMap::FACTOR_BUSINESS_ID . ' and status = 1 and check_status = -1 order by detail_id desc limit 1';
                    $data = Utility::query($sql);
                    if (Utility::isNotEmpty($data)) {
                        switch ($data[0]['role_id']) {
                            case ConstantMap::BOARD_FINANCE_LEADER_ROLE_ID:
                                $status = 6;
                                break;
                            case ConstantMap::FINANCE_LEADER_ROLE_ID:
                                $status = 8;
                                break;
                            case ConstantMap::CASHIER_ROLE_ID:
                                $status = 10;
                                break;
                        }
                    }
                } else {
                    $status = $factorModel->status;
                }
            }
        }

        return $status;
    }*/

    /**
     * @desc 计算应还利息
     * @param int $factorId
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    /*public static function calculateInterest($factorId, $startDate = null, $endDate = null) {
        $interest = 0;
        if(Utility::checkQueryId($factorId)) {
            $factor = Factor::model()->with('factorReturn')->findByPk($factorId);
            $lastReturnInfo = FactoringReturnService::getLastReturnDate($factorId);
            if($lastReturnInfo['res'] == ConstantMap::VALID) {
                $lastReturnDate = $lastReturnInfo['last_return_date'];
                if(!empty($factor)) {
                    if(empty($startDate) || strtotime($startDate) < strtotime($lastReturnDate))
                        $startDate = $lastReturnDate;
                    if(empty($endDate))
                        $endDate = Utility::getDate();
                    if(strtotime($endDate) < strtotime($lastReturnDate)) {
                        $endDate = $lastReturnDate;
                    }
                    if($factor->amount > 0){
                        $balanceCapital = $factor->amount - $factor->return_capital;
                        $days = Utility::diffDays360($startDate, $endDate);
                        $interest = round($balanceCapital * $factor->rate / 100 / 360 * $days + $factor->balance_interest);
                    }
                }
            }
        }

        return $interest;
    }*/

    /**
     * @desc 计算指定日期内应还利息
     * @param int $detailId
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public static function calculatePeriodInterest($detailId, $startDate = null, $endDate = null) {
        if (!Utility::checkQueryId($detailId)) {
            return null;
        }

        $factorDetail = FactorDetail::model()->with('factorReturn')->findByPk($detailId, 't.status >= :status', array('status' => FactorDetail::STATUS_PASS));
        if (empty($factorDetail)) {
            return null;
        }

        if(!empty($startDate) && !empty($endDate) && strtotime($startDate) > strtotime($endDate)) {
            return 0;
        }

        $contractPayDate = FactoringReturnService::getContractPayDate($detailId);
        if (empty($contractPayDate)) {
            return 0;
        }

        if (empty($startDate) || strtotime($startDate) < strtotime($contractPayDate)) {
            $startDate = $contractPayDate;
        }

        if (empty($endDate)) {
            $endDate = date("Y-m-d");
        }

        if (strtotime($endDate) < strtotime($contractPayDate)) {
            return 0;
        }
        $returnAmount = 0;
        $interest = 0;
        if ($factorDetail->amount > 0) {
            if (Utility::isNotEmpty($factorDetail->factorReturn)) {
                $end = end($factorDetail->factorReturn);
                if ($factorDetail->status == FactorDetail::STATUS_RETURNED && strtotime($endDate) >= strtotime($end->return_date)) {
                    $endDate = $end->return_date;
                }
                foreach ($factorDetail->factorReturn as $key => $row) {
                    if ($row->status == FactorReturn::STATUS_SUBMIT && strtotime($row->return_date) < strtotime($endDate)) {
                        $returnAmount += $row->capital_amount;
                        if (strtotime($row->return_date) >= strtotime($startDate)) {
                            $days = Utility::diffDays360($startDate, $row->return_date);
                            $days = $days < 0 ? 1 : $days;
                            $interest += $row->capital_amount * $factorDetail->rate * $days / 360;
                        }
                    }
                }
            }

            $days = Utility::diffDays360($startDate, $endDate);
            $days = $days < 0 ? 1 : $days;
            if ($factorDetail->amount > 0) {
                $interest += ($factorDetail->amount - $returnAmount) * $factorDetail->rate * $days / 360;
            }
        }
        $interest += $factorDetail->balance_interest;

        return round($interest);
    }

    /**
     * 获取查询用的审核节点状态
     * @return array
     */
    public static function getSearchCheckStatus()
    {
        $nodes=FlowService::getFlowNodeModels(FlowService::BUSINESS_FACTORING);
        $searchStatus=array();
        //$searchStatus[Factor::STATUS_NEW]="";
        foreach ($nodes as $n)
        {
            $searchStatus["c_".$n->node_id]="待审核(".$n->node_name.")";
            $searchStatus["b_".$n->node_id]="已驳回(".$n->node_name.")";
        }

        return $searchStatus;
    }

    public static function deleteFactor($factorId, $factorModel=null) {
        if (!Utility::checkQueryId($factorId) || $factorId<1) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }
        if(empty($factorModel)) {
            $factorModel = Factor::model()->findByPk($factorId);
        }
        if(empty($factorModel)) {
            BusinessException::throw_exception(OilError::$FACTOR_NOT_EXIST, array('factor_id' => $factorId));
        }

        if($factorModel->status >= Factor::STATUS_SUBMIT){
            BusinessException::throw_exception(OilError::$FACTOR_NOT_ALLOW_DELETE);
        }

        $res = $factorModel->delete();
        if(!$res) {
            BusinessException::throw_exception(OilError::$OPERATE_FAILED, array('reason' => $res));
        }

        return true;
    }
}