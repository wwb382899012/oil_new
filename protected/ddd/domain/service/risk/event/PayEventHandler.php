<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:25
 * Describe：
 */

namespace ddd\domain\service\risk\event;


use ddd\domain\iRepository\payment\IPayConfirmRepository;
use ddd\domain\service\risk\IAmountEventHandler;
use ddd\domain\service\risk\PartnerAmountSourceCategoryEnum;
use ddd\infrastructure\DIService;
use ddd\repository\payment\PayConfirmRepository;

class PayEventHandler implements IAmountEventHandler
{
    public $event;
    public $contractEntity;

    public function __construct($event = null)
    {
        if (!empty($event))
        {
            $this->event = $event;
        }
        $this->contractEntity = DIService::getRepository(IPayConfirmRepository::class)::getContract($this->event->sender->payment_id);
    }

    function getPartnerId()
    {
         return $this->contractEntity->partner_id;
    }

    function getAmount()
    {
        return $this->event->sender->amount_cny;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Payment;
    }

    function getRelationId()
    {
        return $this->event->sender->payment_id;
    }

    function getContractInfo()
    {
        return array(
            'contract_id' => $this->contractEntity->contract_id,
            'project_id' => $this->contractEntity->project_id,
            'corporation_id' => $this->contractEntity->corporation_id
        );
    }
}