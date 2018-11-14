<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:09
 * Describe：
 */

namespace ddd\repository\risk;


use ddd\domain\entity\risk\PartnerContractAmount;

class PartnerContractAmountRepository extends PartnerAmountRepository
{
    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new PartnerContractAmount();
    }

    function getType()
    {
        // TODO: Implement getType() method.
        return \PartnerAmount::TYPE_CONTRACT;

    }



}