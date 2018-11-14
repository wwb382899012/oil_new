<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 9:52
 * Describe：
 */

namespace ddd\domain\entity\risk;


use ddd\Common\IAggregateRoot;

class PartnerUsedAmountLog extends PartnerAmountLog implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "id";
    }

    function setId($value)
    {
        $this->id=$value;
    }

    public static function create(PartnerUsedAmount $partnerAmount=null)
    {
        $obj= new PartnerUsedAmountLog();
        if(!empty($partnerAmount))
            $obj->amountId=$partnerAmount->getId();

        return $obj;
    }
}