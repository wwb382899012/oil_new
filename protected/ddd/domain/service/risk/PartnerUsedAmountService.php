<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:20
 * Describe：
 */

namespace ddd\domain\service\risk;


use ddd\repository\risk\PartnerUsedAmountLogRepository;
use ddd\repository\risk\PartnerUsedAmountRepository;

class PartnerUsedAmountService extends PartnerAmountService
{
    protected function init()
    {
        $this->repository=new PartnerUsedAmountRepository();
        $this->logRepository=new PartnerUsedAmountLogRepository();
    }
}