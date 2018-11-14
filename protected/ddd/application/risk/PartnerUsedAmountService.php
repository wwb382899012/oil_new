<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：
 */

namespace ddd\application\risk;


use ddd\application\dto\partnerAmount\PartnerUsedAmountDTO;
use ddd\Common\Application\TransactionService;
use ddd\domain\entity\Partner;
use ddd\domain\entity\risk\PartnerUsedAmount;
use ddd\repository\risk\PartnerUsedAmountRepository;

class PartnerUsedAmountService extends TransactionService
{

    protected $partnerUsedAmountRepository;

    public function __construct()
    {
        $this->partnerUsedAmountRepository = new PartnerUsedAmountRepository();
    }

    /**
     * @desc 创建合作方合同额度
     * @param Partner $partner
     * @return bool|PartnerUsedAmount
     * @throws \Exception
     */
    public function createPartnerUsedAmount(Partner $partner)
    {
        return PartnerUsedAmount::create($partner);
    }


    /**
     * @desc 保存合作方合同额度
     * @param PartnerUsedAmountDTO $partnerUsedAmount
     * @return array|bool|mixed
     * @throws \Exception
     */
    /*public function savePartnerUsedAmount(PartnerUsedAmountDTO $partnerUsedAmount)
    {
        if (!$partnerUsedAmount->validate())
        {
            return $partnerUsedAmount->getErrors();
        }

        $entity = $partnerUsedAmount->toEntity();
        if (!$entity->validate())
        {
            return $entity->getErrors();
        }

        try
        {
            $this->beginTransaction();

            $this->partnerUsedAmountRepository->store($entity);

            $this->commitTransaction();

            return true;
        } catch (\Exception $e)
        {
            $this->rollbackTransaction();
            if ($this->isInOutTrans)
            {
                throw $e;
            } else
            {
                return false;
            }
        }
    }*/
}