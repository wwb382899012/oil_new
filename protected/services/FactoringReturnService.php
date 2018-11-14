<?php

/**
 * Desc: 保理回款
 * User: susiehuang
 * Date: 2017/10/26 0009
 * Time: 10:03
 */
class FactoringReturnService {
    /**
     * @desc 计算保理单总的回款金额
     * @param int $factor_id
     * @return array [
     *      'total_amount' => 1000              #总的回款金额
     *      'total_capital_amount' => 800       #总的回款本金
     *      'total_interest' => 200             #总的回款利息
     *      'total_balance_interest' => 200     #总的未还利息
     * ]
     */
    public static function calculateFactorReturnAmount($factor_id) {
        $res = array('total_amount' => 0, 'total_capital_amount' => 0, 'total_interest' => 0, 'total_balance_interest' => 0);
        if (Utility::checkQueryId($factor_id)) {
            $sql = 'select sum(return_amount) as total_amount, sum(return_capital) as total_capital_amount, sum(return_interest) as total_interest, sum(balance_interest) as total_balance_interest from t_factoring_detail where factor_id = ' . $factor_id;
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                $res['total_amount'] = $data[0]['total_amount'];
                $res['total_capital_amount'] = $data[0]['total_capital_amount'];
                $res['total_interest'] = $data[0]['total_interest'];
                $res['total_balance_interest'] = $data[0]['total_balance_interest'];
            }
        }

        return $res;
    }

    /**
     * @desc 计算保理对接单总的回款金额
     * @param int $detail_id
     * @return array [
     *      'total_amount' => 1000              #总的回款金额
     *      'total_capital_amount' => 800       #总的回款本金
     *      'total_interest' => 200             #总的回款利息
     * ]
     */
    public static function calculateFactorDetailReturnAmount($detail_id) {
        $res = array('total_amount' => 0, 'total_capital_amount' => 0, 'total_interest' => 0);
        if (Utility::checkQueryId($detail_id)) {
            $sql = 'select sum(amount) as total_amount, sum(capital_amount) as total_capital_amount, sum(interest) as total_interest from t_factoring_return where detail_id = ' . $detail_id . ' and status=' . FactorReturn::STATUS_SUBMIT;
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                $res['total_amount'] = $data[0]['total_amount'];
                $res['total_capital_amount'] = $data[0]['total_capital_amount'];
                $res['total_interest'] = $data[0]['total_interest'];
            }
        }

        return $res;
    }

    /**
     * @desc 获取上一次保理回款日期
     * @param int $detail_id
     * @return array
     */
    public static function getLastReturnDate($detail_id) {
        $res = array('res' => ConstantMap::VALID, 'last_return_date' => '', 'msg' => '');
        if (Utility::checkQueryId($detail_id)) {
            $factorDetail = FactorDetail::model()->findByPk($detail_id);
            if (!empty($factorDetail->detail_id)) {
                $contractPayDate = FactoringReturnService::getContractPayDate($detail_id);
                if (!empty($contractPayDate)) {
                    $res['last_return_date'] = $contractPayDate;

                    $sql = 'select return_date from t_factoring_return where detail_id = ' . $detail_id . ' and status = ' . FactorReturn::STATUS_SUBMIT . ' order by id desc limit 1';
                    $data = Utility::query($sql);
                    if (Utility::isNotEmpty($data)) {
                        $res['last_return_date'] = $data[0]['return_date'];
                    }
                } else {
                    $res['res'] = ConstantMap::INVALID;
                    $res['msg'] = BusinessError::outputError(OilError::$FACTOR_NOT_ACTUAL_PAY);
                }
            } else {
                $res['res'] = ConstantMap::INVALID;
                $res['msg'] = BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $detail_id));
            }
        } else {
            $res['res'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);
        }

        return $res;
    }

    /**
     * @desc 获取回款明细实际期限
     * @param int $id
     * @return int
     */
    public static function getActualReturnedPeriod($id) {
        $period = 0;
        if (Utility::checkQueryId($id)) {
            $factorReturn = FactorReturn::model()->findByPk($id);
            if (!empty($factorReturn->id)) {
                $curr_return_date = $factorReturn->return_date;
                $last_return_date = $factorReturn->factorDetail->pay_date;
                $sql = 'select return_date from t_factoring_return where detail_id = ' . $factorReturn->detail_id . ' and id < ' . $id . ' order by id desc limit 1';
                $lastRes = Utility::query($sql);
                if (Utility::isNotEmpty($lastRes)) {
                    $last_return_date = $lastRes[0]['return_date'];
                }
                $period = Utility::diffDays360($last_return_date, $curr_return_date);
            }
        }

        return $period;
    }

    /**
     * @desc 检查是否可保理回款
     * @param int $detail_id
     * @return bool
     */
    public static function checkIsCanAddFactorReturn($detail_id) {
        if (Utility::checkQueryId($detail_id)) {
            $factorReturns = FactorReturn::model()->findAll('detail_id = :detailId and status = :status', array('detailId' => $detail_id, 'status' => FactorReturn::STATUS_NEW));
            if (Utility::isNotEmpty($factorReturns)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @desc 获取剩余回款金额信息
     * @param int $detailId
     * @param string $returnDate
     * @param object $factorDetail
     * @return array
     * @throws
     */
    public static function getBalanceAmount($detailId, $returnDate, $factorDetail = null) {
        $lastReturnDateInfo = FactoringReturnService::getLastReturnDate($detailId);
        if ($lastReturnDateInfo['res'] == ConstantMap::INVALID) {
            throw new Exception($lastReturnDateInfo['msg']);
        }
        $lastReturnDate = $lastReturnDateInfo['last_return_date'];
        if (strtotime($returnDate) < strtotime($lastReturnDate)) {
            BusinessException::throw_exception(OilError::$RETURN_DATE_LT_LAST_RETURN_DATE, array('return_date' => $returnDate, 'last_return_date' => $lastReturnDate));
        }

        if (empty($factorDetail)) {
            $factorDetail = FactorDetail::model()->findByPk($detailId);
        }
        if (empty($factorDetail->detail_id)) {
            BusinessException::throw_exception(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $detailId));
        }
        $res['capital_amount'] = $factorDetail->amount - $factorDetail->return_capital;
        $res['interest'] = FactoringService::calculatePeriodInterest($detailId, $lastReturnDate, $returnDate);
        $res['amount'] = $res['capital_amount'] + $res['interest'];

        return $res;
    }

    /**
     * @desc 获取保理信息
     * @param int $detail_id
     * @param object $factorDetail
     * @return array
     */
    public static function getFactorDetailInfo($detail_id, $factorDetail = null) {
        $res = array();
        if (Utility::checkQueryId($detail_id)) {
            if (empty($factorDetail)) {
                $factorDetail = FactorDetail::model()->findByPk($detail_id);
            }
            if (!empty($factorDetail->detail_id)) {
                $today = Utility::getDate();
                $res['contract_pay_date'] = $factorDetail->pay_date;
                $res['curr_return_date'] = strtotime($res['contract_pay_date']) < strtotime($today) ? $today : $res['contract_pay_date'];
                $sql = 'select return_date from t_factoring_return where detail_id = ' . $detail_id . ' and status = ' . FactorReturn::STATUS_SUBMIT . ' order by id desc limit 1';
                $data = Utility::query($sql);
                if (Utility::isNotEmpty($data)) {
                    $res['last_return_date'] = $data[0]['return_date'];
                    $res['curr_return_date'] = strtotime($data[0]['return_date']) <= strtotime($today) ? $today : $data[0]['return_date'];
                }
                $res['contract_return_date'] = $factorDetail->return_date;
                $res['factor_id'] = $factorDetail->factor_id;
                $res['rate'] = $factorDetail->rate * 100 . ' %';
                $res['factor_amount'] = '￥ ' . $factorDetail->amount / 100;
                $res['factor_interest'] = '￥ ' . $factorDetail->interest / 100;
                $res['factor_return_amount'] = '￥ ' . $factorDetail->return_amount / 100;
                $res['factor_return_capital_amount'] = '￥ ' . $factorDetail->return_capital / 100;
                $res['factor_return_interest'] = '￥ ' . $factorDetail->return_interest / 100;
                $res['factor_balance_capital'] = '￥ ' . ($factorDetail->amount - $factorDetail->return_capital) / 100;
            }
        }

        return $res;
    }

    /**
     * @desc 保理单对接付款单实际放款日
     * @param int $detail_id
     * @return string
     */
    /*public static function getActualPaymentDay($detail_id) {
        $actual_pay_date = '';
        if (Utility::checkQueryId($detail_id)) {
            $factorDetail = FactorDetail::model()->findByPk($detail_id);
            if (!empty($factorDetail->detail_id)) {
                $sql = 'select pay_date from t_payment where apply_id = ' . $factorDetail->apply_id . ' order by payment_id asc limit 1';
                $data = Utility::query($sql);
                $actual_pay_date = Utility::isNotEmpty($data) ? $data[0]['pay_date'] : '';
            }
        }

        return $actual_pay_date;
    }*/

    /**
     * @desc 保理单对接单合同回款日
     * @param int $detail_id
     * @return string
     */
    public static function getContractPayDate($detail_id) {
        $contract_return_date = '';
        if (Utility::checkQueryId($detail_id)) {
            $factorDetail = FactorDetail::model()->findByPk($detail_id);
            if (!empty($factorDetail->detail_id)) {
                return $factorDetail->pay_date;
            }
        }

        return $contract_return_date;
    }


}