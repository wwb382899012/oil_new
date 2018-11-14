<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:25
 * Describe：
 */

namespace ddd\domain\service\risk\event;


use ddd\domain\service\risk\IAmountEventHandler;
use ddd\domain\service\risk\PartnerAmountSourceCategoryEnum;

class ContractRejectEventHandler implements IAmountEventHandler
{
    public $event;

    public function __construct($event = null)
    {
        if (!empty($event))
        {
            $this->event = $event;
        }
    }

    function getPartnerId()
    {
        return $this->event->sender->partner_id;
    }

    function getAmount()
    {
        return $this->event->sender->amount_cny * -1;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Contract_Reject;
    }

    function getRelationId()
    {
        return $this->event->sender->contract_id;
    }

    function getContractInfo()
    {
        return array(
            'contract_id' => $this->event->sender->contract_id,
            'project_id' => $this->event->sender->project_id,
            'corporation_id' => $this->event->sender->corporation_id
        );
    }
}