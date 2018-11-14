<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：
 */

namespace ddd\application;


use ddd\application\dto\partner\PartnerDTO;
use ddd\application\dto\partnerAmount\PartnerContractAmountDTO;
use ddd\application\dto\partnerAmount\PartnerUsedAmountDTO;
use ddd\application\risk\PartnerContractAmountService;
use ddd\application\risk\PartnerUsedAmountService;
use ddd\Common\Application\TransactionService;
use ddd\domain\entity\Partner;
use ddd\domain\iRepository\IPartnerRepository;
use ddd\domain\iRepository\risk\IPartnerContractAmountRepository;
use ddd\domain\iRepository\risk\IPartnerUsedAmountRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\risk\PartnerContractAmountRepository;
use ddd\repository\risk\PartnerUsedAmountRepository;

class PartnerService extends TransactionService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = DIService::getRepository(IPartnerRepository::class);
    }

    /**
     * @desc 合作方准入申请通过
     * @param PartnerDTO $partner
     * @return bool
     * @throws \Exception
     */
    /*public function passPartner(PartnerDTO $partner)
    {
        if (!$partner->validate())
        {
            return $partner->getErrors();
        }

        $entity = $partner->toEntity();
        if (!$entity->validate())
        {
            return $entity->getErrors();
        }

        try
        {
            $this->beginTransaction();
            //生成合作方合同额度
            $partnerContractAmountService = new PartnerContractAmountService();
            $partnerContractAmountEntity = PartnerContractAmountRepository::repository()->findByPartnerId($entity->partner_id);
            if (empty($partnerContractAmountEntity))
            {
                $partnerContractAmountEntity = $partnerContractAmountService->createPartnerContractAmount($entity);
            }
            $partnerContractAmountEntity->credit_amount = $entity->credit_amount;
            //            $partnerContractAmountEntity->available_amount = $entity->credit_amount;
            $partnerContractAmountDto = new PartnerContractAmountDTO();
            $partnerContractAmountDto->fromEntity($partnerContractAmountEntity);
            $partnerContractAmountService->savePartnerContractAmount($partnerContractAmountDto);

            //生成合作方实际占用额度
            $partnerUsedAmountService = new PartnerUsedAmountService();
            $partnerUsedAmountEntity = PartnerUsedAmountRepository::repository()->findByPartnerId($entity->partner_id);
            if (empty($partnerUsedAmountEntity))
            {
                $partnerUsedAmountEntity = $partnerContractAmountService->createPartnerContractAmount($entity);
            }
            $partnerUsedAmountEntity->credit_amount = $entity->credit_amount;
            //            $partnerUsedAmountEntity->available_amount = $entity->credit_amount;
            $partnerUsedAmountDto = new PartnerUsedAmountDTO();
            $partnerUsedAmountDto->fromEntity($partnerUsedAmountEntity);
            $partnerUsedAmountService->savePartnerUsedAmount($partnerUsedAmountDto);

            $this->commitTransaction();
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

    /**
     * @desc 合作方准入申请通过
     * @param $partnerId
     * @param Partner $partnerEntity
     * @return bool
     * @throws \Exception
     */
    public function passPartner($partnerId, Partner $partnerEntity = null)
    {
        try
        {
            if (empty($partnerEntity))
            {
                $partnerEntity = $this->repository->findByPk($partnerId);
                if (empty($partnerEntity))
                {
                    throw new ZEntityNotExistsException($partnerId, Partner::class);
                }
            }

            $this->beginTransaction();
            //生成合作方合同额度
            $partnerContractAmountEntity = DIService::getRepository(IPartnerContractAmountRepository::class)->findByPartnerId($partnerId);
            if (empty($partnerContractAmountEntity))
            {
                $partnerContractAmountEntity = PartnerContractAmountService::service()->createPartnerContractAmount($partnerEntity);
            }
            $partnerContractAmountEntity->credit_amount = $partnerEntity->credit_amount;
            DIService::getRepository(IPartnerContractAmountRepository::class)->store($partnerContractAmountEntity);

            //生成合作方实际占用额度
            $partnerUsedAmountEntity = DIService::getRepository(IPartnerUsedAmountRepository::class)->findByPartnerId($partnerEntity->partner_id);
            if (empty($partnerUsedAmountEntity))
            {
                $partnerUsedAmountEntity = PartnerUsedAmountService::service()->createPartnerUsedAmount($partnerEntity);
            }
            $partnerUsedAmountEntity->credit_amount = $partnerEntity->credit_amount;
            DIService::getRepository(IPartnerUsedAmountRepository::class)->store($partnerUsedAmountEntity);
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
    }
}