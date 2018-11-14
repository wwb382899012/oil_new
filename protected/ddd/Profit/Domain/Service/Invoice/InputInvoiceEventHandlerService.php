<?php
/**
 * User: liyu
 * Date: 2018/8/10
 * Time: 15:01
 * Desc: InputInvoiceEventHandlerService.php  预估利润报表  收票金额计算
 */

namespace ddd\Profit\Domain\Service\Invoice;


use ddd\Common\Domain\Value\Money;
use ddd\infrastructure\DIService;
use ddd\Profit\Application\Estimate\EstimateProfitService;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfit;
use ddd\Profit\Domain\EstimateProfit\IEstimateContractProfitRepository;
use ddd\Profit\Domain\Model\Invoice\InputInvoiceCheckPassEvent;
use ddd\Profit\Repository\Contract\ContractRepository;
use ddd\Split\Domain\Model\Contract\ContractEnum;

class InputInvoiceEventHandlerService
{
    public function onInputInvoiceCheckPass(InputInvoiceCheckPassEvent $event) {
        $entity = $event->sender;
        $contract = ContractRepository::repository()->findByContractId($entity->contract_id);
        $estimateContractProfit = DIService::getRepository(IEstimateContractProfitRepository::class)->findByContractId($entity->contract_id);
        if (empty($estimateContractProfit)) {
            $estimateContractProfit = EstimateContractProfit::create($contract);
        }
        $buyInvoiceAmount = $entity->getBuyInvoiceAmount();
        if ($entity->type == ContractEnum::BUY_CONTRACT) {//采购合同
            if($estimateContractProfit->invoice_amount){
                $estimateContractProfit->invoice_amount= $estimateContractProfit->invoice_amount->addMoney(new Money($buyInvoiceAmount));
            }else{
                $estimateContractProfit->invoice_amount=new Money($buyInvoiceAmount);
            }

        }
//        else {
//            $sellContractProfit->sell_invoice_amount = new Price($entity->getSellInvoiceAmount() + $sellContractProfit->sell_invoice_amount->price);
//        }
        DIService::getRepository(IEstimateContractProfitRepository::class)->store($estimateContractProfit);
        \AMQPService::publishEstimateContractProfit($contract->project_id);
    }
}