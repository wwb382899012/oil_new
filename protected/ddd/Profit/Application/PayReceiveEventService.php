<?php
/**
 * User: liyu
 * Date: 2018/8/9
 * Time: 17:58
 * Desc: PayReceiveEventService.php
 */

namespace ddd\Profit\Application;


use ddd\Common\Application\TransactionService;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\Profit\Domain\Model\Invoice\InvoiceBase;
use ddd\Profit\Domain\Model\Payment\PayReceiveAmount;
use ddd\Profit\Domain\Model\Profit\ISellContractProfitRepository;
use ddd\Profit\Domain\Model\Profit\SellContractProfit;
use ddd\Profit\Domain\Service\PayReceiveAmountService;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\domain\iRepository\contract\IContractRepository;

class PayReceiveEventService extends TransactionService
{
    /**
     * @desc 银行流水认领成功
     * @param $contractId
     * @param $subject 用途
     * @return mixed|string|void
     * @throws \Exception
     */
    public function onReceiveConfirm($contractId, $subject = 0) {
        if (empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if (empty($contract)) {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $entity = PayReceiveAmount::create($contract, $subject);
        try {
            $entity->receiveConfirm();
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @desc 合同下付款实付完成
     * @param $contractId
     * @return mixed|string|void
     * @throws \Exception
     */
    public function onPayConfirm($contractId, $subject = 0) {
        if (empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if (empty($contract)) {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $entity = PayReceiveAmount::create($contract, $subject);
        try {
            $entity->payConfirm();
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @desc 后补项目合同付款认领成功
     * @param $contractId
     * @return mixed|string|void
     * @throws \Exception
     */
    public function onPayClaim($contractId, $subject = 0) {
        if (empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if (empty($contract)) {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $entity = PayReceiveAmount::create($contract, $subject);
        try {
            $entity->payClaim();
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据合同ID修复历史合同 收付款 利润
     * @param $contractId
     */
    public function contractProfitDataRepair($contractId) {
        if (empty($contractId)) {
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        }
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if (empty($contract)) {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $sellContractProfit = SellContractProfit::create($contract);
        $payReceiveEntity = PayReceiveAmount::create($contract);
        $currency = 0;
        $amount = PayReceiveAmountService::calcPayReceiveAmount($payReceiveEntity);
        if ($payReceiveEntity->type == ContractEnum::BUY_CONTRACT) {//采购合同
            $sellContractProfit->pay_amount = new Price($amount);
        } else {
            $sellContractProfit->receive_amount = new Price($amount);;
        }
        //杂费
        $miscellaneousFee = PayReceiveAmountService::calcMiscellaneousFee($payReceiveEntity);
        $sellContractProfit->miscellaneous_fee = new Price($miscellaneousFee);
        DIService::getRepository(ISellContractProfitRepository::class)->store($sellContractProfit);

    }


}