<?php
/**
 * User: liyu
 * Date: 2018/8/10
 * Time: 15:01
 * Desc: InputInvoiceEventHandlerService.php
 */

namespace ddd\Profit\Domain\Service\Invoice;


use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Invoice\InvoiceCheckPassEvent;
use ddd\Profit\Domain\Model\Profit\ISellContractProfitRepository;
use ddd\Profit\Domain\Model\Profit\SellContractProfit;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\domain\iRepository\contract\IContractRepository;

class InvoiceEventHandlerService
{
    public function onInvoiceCheckPass(InvoiceCheckPassEvent $event) {
        $entity = $event->sender;
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($entity->contract_id);
        $sellContractProfit = DIService::getRepository(ISellContractProfitRepository::class)->findByContractId($entity->contract_id);
        if (empty($sellContractProfit)) {
            $sellContractProfit = SellContractProfit::create($contract);
        }
        $currency = 0;
        $se=$entity->getSellInvoiceAmount();
        if ($entity->type == ContractEnum::BUY_CONTRACT) {//采购合同
            $sellContractProfit->buy_invoice_amount = new Price($entity->getBuyInvoiceAmount() + $sellContractProfit->buy_invoice_amount->price);
        } else {
            $sellContractProfit->sell_invoice_amount = new Price($entity->getSellInvoiceAmount() + $sellContractProfit->sell_invoice_amount->price);
        }
        DIService::getRepository(ISellContractProfitRepository::class)->store($sellContractProfit);
    }
}