<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:53
 * Describe：
 */

namespace ddd\repository\risk;


use ddd\domain\entity\risk\PartnerUsedAmountLog;

class PartnerUsedAmountLogRepository extends PartnerAmountLogRepository
{

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new PartnerUsedAmountLog();
    }

    function getType()
    {
        // TODO: Implement getType() method.
        return \PartnerAmount::TYPE_USED;
    }

}