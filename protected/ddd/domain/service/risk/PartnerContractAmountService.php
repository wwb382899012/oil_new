<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 16:30
 * Describe：
 */

namespace ddd\domain\service\risk;


use ddd\repository\risk\PartnerContractAmountLogRepository;
use ddd\repository\risk\PartnerContractAmountRepository;

class PartnerContractAmountService extends PartnerAmountService
{
    protected function init()
    {
        $this->repository=new PartnerContractAmountRepository();
        $this->logRepository=new PartnerContractAmountLogRepository();
    }



}