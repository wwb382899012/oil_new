<?php
/**
 * User: liyu
 * Date: 2018/8/10
 * Time: 14:59
 * Desc: InvoiceEventService.php
 */

namespace ddd\Profit\Application;


use ddd\Common\Application\TransactionService;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Invoice\InvoiceBase;
use ddd\Profit\Domain\Model\Profit\ISellContractProfitRepository;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\Split\Domain\Model\Contract\ContractEnum;

class InvoiceEventService extends TransactionService
{
    /**
     * @desc 进项票审核通过
     * @param $contractId
     * @return mixed|string|void
     * @throws \Exception
     */
    public function onInputInvoiceCheckPass($contractId, $invoice_application_id) {
        if (empty($contractId) || empty($invoice_application_id))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if (empty($contract)) {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $entity = InvoiceBase::create($contract, $invoice_application_id);
        try {
            $entity->InputInvoiceCheckPass();
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @desc 销项票开票审核通过
     * @param $contractId
     * @return mixed|string|void
     * @throws \Exception
     */
    public function onInvoiceCheckPass($contractId, $invoice_id) {
        if (empty($contractId) || empty($invoice_id))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if (empty($contract)) {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $entity = InvoiceBase::create($contract, 0, $invoice_id);
        try {
            $entity->InvoiceCheckPass();
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @desc 根据合同ID修复历史合同  发票 利润
     * @param $contractId
     * @param int $invoiceApplicationId 发票申请ID(开票时不要传)
     * @param int $invoiceId 开票ID
     * @return mixed|string
     * @throws \Exception
     */
    public function contractProfitDataRepair($contractId, $invoiceApplicationId = 0, $invoiceId = 0) {
        if (empty($contractId)) {
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        }
        $contract = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if (empty($contract)) {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $sellContractProfit = DIService::getRepository(ISellContractProfitRepository::class)->findByContractId($contractId);
        if (empty($sellContractProfit)) {
            $sellContractProfit = SellContractProfit::create($contract);
        }
        $currency = 0;
        $invoiceBase = InvoiceBase::create($contract, $invoiceApplicationId, $invoiceId);
        if ($contract->type == ContractEnum::BUY_CONTRACT) {//采购合同
            $sellContractProfit->buy_invoice_amount = new Price($invoiceBase->getBuyInvoiceAmount() + $sellContractProfit->buy_invoice_amount->price);
        } else {
            $sellContractProfit->sell_invoice_amount = new Price($invoiceBase->getSellInvoiceAmount() + $sellContractProfit->sell_invoice_amount->price);
        }
        DIService::getRepository(ISellContractProfitRepository::class)->store($sellContractProfit);

    }
}