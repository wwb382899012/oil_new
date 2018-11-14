<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 9:50
 * Describe：
 */

namespace ddd\domain\entity\risk;


use ddd\Common\IAggregateRoot;

class PartnerContractAmountLog extends PartnerAmountLog implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "log_id";
    }

    function setId($value)
    {
        $this->id=$value;
    }

    public static function create(PartnerContractAmount $partnerAmount=null)
    {
        $obj= new PartnerContractAmountLog();
        if(!empty($partnerAmount))
            $obj->amountId=$partnerAmount->getId();
        $obj->create_time=date('Y-m-d H:i:s');
        return $obj;
    }

}