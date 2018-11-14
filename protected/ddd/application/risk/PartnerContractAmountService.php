<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：
 */

namespace ddd\application\risk;


use ddd\application\dto\partnerAmount\PartnerContractAmountDTO;
use ddd\Common\Application\TransactionService;
use ddd\domain\entity\Partner;
use ddd\domain\entity\risk\PartnerContractAmount;
use ddd\repository\risk\PartnerContractAmountRepository;

class PartnerContractAmountService extends TransactionService
{

    protected $partnerContractAmountRepository;

    public function __construct()
    {
        $this->partnerContractAmountRepository = new PartnerContractAmountRepository();
    }

    /**
     * @desc 创建合作方合同额度
     * @param Partner $partner
     * @return bool|PartnerContractAmount
     * @throws \Exception
     */
    public function createPartnerContractAmount(Partner $partner)
    {
        return PartnerContractAmount::create($partner);
    }


    /**
     * @desc 保存合作方合同额度
     * @param PartnerContractAmountDTO $partnerContractAmount
     * @return array|bool|mixed
     * @throws \Exception
     */
    /*public function savePartnerContractAmount(PartnerContractAmountDTO $partnerContractAmount)
    {
        if (!$partnerContractAmount->validate())
        {
            return $partnerContractAmount->getErrors();
        }

        $entity = $partnerContractAmount->toEntity();
        if (!$entity->validate())
        {
            return $entity->getErrors();
        }

        try
        {
            $this->beginTransaction();

            $this->partnerContractAmountRepository->store($entity);

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