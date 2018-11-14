<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/20 16:27
 * Describe：
 */

namespace ddd\domain\service\risk\event;


use ddd\domain\service\risk\IAmountEventHandler;
use ddd\domain\service\risk\PartnerAmountSourceCategoryEnum;

class ContractSubmitEventHandler implements IAmountEventHandler
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
        return $this->event->sender->amount_cny;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Contract_Submit;
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