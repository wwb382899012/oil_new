<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/10 11:04
 * Describe：
 */

namespace ddd\domain\iRepository\contract;


use ddd\domain\entity\contract\ContractStat;
use ddd\Common\Domain\IRepository;

interface IContractStatRepository extends IRepository
{
    function addAndSaveAmountOut(ContractStat $contractStat,$amount);

    function addAndSaveAmountIn(ContractStat $contractStat,$amount);

    function addAndSaveGoodsInAmount(ContractStat $contractStat,$amount);

    function addAndSaveGoodsOutAmount(ContractStat $contractStat,$amount);

    function addAndSaveInvoiceInAmount(ContractStat $contractStat,$amount);

    function addAndSaveInvoiceOutAmount(ContractStat $contractStat,$amount);

}