<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:51
 * Describe：
 */

namespace ddd\repository\risk;


use ddd\domain\entity\risk\PartnerContractAmountLog;

class PartnerContractAmountLogRepository extends PartnerAmountLogRepository
{

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new PartnerContractAmountLog();
    }

    function getType()
    {
        // TODO: Implement getType() method.
        return \PartnerAmount::TYPE_CONTRACT;
    }



}