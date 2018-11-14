<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:25
 * Describe：
 */

namespace ddd\domain\service\risk\event;


use ddd\domain\service\risk\IAmountEventHandler;
use ddd\domain\service\risk\PartnerAmountSourceCategoryEnum;
use ddd\repository\contract\ContractRepository;

class PayClaimedEventHandler implements IAmountEventHandler
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
        $contractEntity = ContractRepository::repository()->findByPk($this->event->sender->contract_id);
        if (!empty($contractEntity))
        {
            return $contractEntity->partner_id;
        }

        return 0;
    }

    function getAmount()
    {
        return $this->event->sender->amount_cny;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Pay_Claim;
    }

    function getRelationId()
    {
        return $this->event->sender->claim_id;
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