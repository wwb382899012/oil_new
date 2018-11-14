<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:10
 * Describe：
 */

namespace ddd\repository\risk;


use ddd\domain\entity\risk\PartnerUsedAmount;

class PartnerUsedAmountRepository extends PartnerAmountRepository
{

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new PartnerUsedAmount();
    }

    function getType()
    {
        // TODO: Implement getType() method.
        return \PartnerAmount::TYPE_USED;
    }




}