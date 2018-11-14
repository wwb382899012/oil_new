<?php
/**
 * User: liyu
 * Date: 2018/8/9
 * Time: 17:13
 * Desc: PayReceiveAmountService.phpce.php
 */

namespace ddd\Profit\Domain\Service;


use ddd\Common\Domain\BaseService;
use ddd\Profit\Domain\Model\Payment\PayReceiveAmount;
use ddd\Split\Domain\Model\Contract\ContractEnum;

class PayReceiveAmountService extends BaseService
{
    /**
     * @desc 杂费Array
     * @var array
     */
    public static $miscellaneous_subject = true;

    /**
     * @desc 计算收付款金额
     * @param PayReceiveAmount $entity
     * @return int
     */
    public static function calcPayReceiveAmount(PayReceiveAmount $entity) {
        $paymentAmount = self::calcPaymentAmount($entity);
        $receiveConfirmAmount = $entity->getReceiveConfirmAmount();
        $amount = 0;
        if ($entity->type == ContractEnum::BUY_CONTRACT) {//采购合同
            //已付上游款
            $amount = $paymentAmount - $receiveConfirmAmount;
        } else {
            //已收款
            $amount = $receiveConfirmAmount - $paymentAmount;
        }
        return $amount;
    }

    /**
     * @desc  计算合同的杂费
     */
    public static function calcMiscellaneousFee(PayReceiveAmount $entity) {
        $miscellaneousFee = 0;
        if ($entity->type == ContractEnum::BUY_CONTRACT) {//采购合同
            //采购合同 杂费  实付总费用-（运输费用+仓储费用+货款费用）实付金额之和+合同已认领（运输费用+货款费用+仓储费用）
            $paymentAmount = self::calcPaymentAmount($entity);
            $receiveConfirmAmount = $entity->getReceiveConfirmAmount();
            $paymentMiscellaneousAmount = self::calcPaymentAmount($entity, self::$miscellaneous_subject);
            $receiveConfirmMiscellaneousAmount = $entity->getReceiveConfirmAmount(self::$miscellaneous_subject);//合同已认领（运输费用+货款费用+仓储费用）
            $miscellaneousFee = ($paymentAmount - $receiveConfirmAmount) - $paymentMiscellaneousAmount + $receiveConfirmMiscellaneousAmount;
        } else {
            //销售合同 杂费 已认领金额总费用-合同（运输费用+仓储费用+货款费用）认领金额之和+合同已实付（运费费用+货款费用+仓储费用）
            $paymentAmount = self::calcPaymentAmount($entity);
            $receiveConfirmAmount = $entity->getReceiveConfirmAmount();
            $receiveConfirmMiscellaneousAmount = $entity->getReceiveConfirmAmount(self::$miscellaneous_subject);
            $paymentMiscellaneousAmount = self::calcPaymentAmount($entity, self::$miscellaneous_subject);
            $miscellaneousFee = ($receiveConfirmAmount - $paymentAmount) - $receiveConfirmMiscellaneousAmount + $paymentMiscellaneousAmount;
        }
        return $miscellaneousFee;
    }

    /**
     * @desc 计算实付总费用
     * @param PayReceiveAmount $entity
     * @param string $subject
     * @return \ddd\Profit\Domain\Model\Payment\Money|int
     */
    private static function calcPaymentAmount(PayReceiveAmount $entity, $subject = '') {
        $payConfirmAmount = $entity->getPayConfirmAmount($subject);//实付金额
        $payClaimAmount = $entity->getPayClaimAmount($subject);//后补认领金额
        return $payConfirmAmount + $payClaimAmount;
    }
}