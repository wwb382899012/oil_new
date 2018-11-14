<?php
/**
 * User: liyu
 * Date: 2018/8/9
 * Time: 16:56
 * Desc: ReceiveConfirmEventHandlerService.php
 */

namespace ddd\Profit\Domain\Service\Payment;


use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Payment\ReceiveConfirmEvent;
use ddd\Profit\Domain\Model\Profit\ISellContractProfitRepository;
use ddd\Profit\Domain\Model\Profit\SellContractProfit;
use ddd\Profit\Domain\Service\PayReceiveAmountService;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\domain\iRepository\contract\IContractRepository;

class ReceiveConfirmEventHandlerService
{
    public function onReceiveConfirm(ReceiveConfirmEvent $event) {
        $entity = $event->sender;
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($entity->contract_id);
        $sellContractProfit = DIService::getRepository(ISellContractProfitRepository::class)->findByContractId($entity->contract_id);
        if (empty($sellContractProfit)) {
            $sellContractProfit = SellContractProfit::create($contract);
        }
        $currency = 0;
        $amount = PayReceiveAmountService::calcPayReceiveAmount($entity);
        if ($entity->type == ContractEnum::BUY_CONTRACT) {//采购合同
            $sellContractProfit->pay_amount = new Price($amount);
        } else {
            $sellContractProfit->receive_amount = new Price($amount);
        }
//        if (in_array($entity->subject, PayReceiveAmountService::$miscellaneous_subject)) {//杂费
            $miscellaneousFee = PayReceiveAmountService::calcMiscellaneousFee($entity);
            $sellContractProfit->miscellaneous_fee = new Price($miscellaneousFee);
//        }
        DIService::getRepository(ISellContractProfitRepository::class)->store($sellContractProfit);
    }

}