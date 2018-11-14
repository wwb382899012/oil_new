<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：
 */

namespace ddd\application\payment;


use ddd\application\dto\payment\PayClaimDTO;
use ddd\application\dto\payment\PayConfirmDTO;
use ddd\domain\entity\payment\PayClaim;
use ddd\domain\entity\payment\PayConfirm;
use ddd\Common\Application\TransactionService;
use ddd\domain\iRepository\payment\IPayClaimRepository;
use ddd\domain\iRepository\payment\IPayConfirmRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\payment\PayConfirmRepository;

class PaymentService extends TransactionService
{

    protected $payConfirmRepository;

    public function __construct()
    {
        $this->payConfirmRepository = new PayConfirmRepository();
    }

    /**
     * @desc 保存付款实付
     * @param PayConfirmDTO $payConfirm
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function savePayConfirm(PayConfirmDTO $payConfirm)
    {
        if (!$payConfirm->validate())
        {
            return $payConfirm->getErrors();
        }

        $entity = $payConfirm->toEntity();
        if (!$entity->validate())
        {
            return $entity->getErrors();
        }

        try
        {
            $this->beginTransaction();

            $this->payConfirmRepository->store($entity);

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

    /**
     * @desc 提交付款实付
     * @param $payment_id
     * @param PayConfirm $payConfirmEntity
     * @return string|bool|mixed
     * @throws \Exception
     */
    public function submitPayConfirm($payment_id, $payConfirmEntity)
    {
        try
        {
            if (empty($payConfirmEntity))
            {
                $payConfirmEntity = DIService::getRepository(IPayConfirmRepository::class)->findByPk($payment_id);
                if (empty($payConfirmEntity->payment_id))
                {
                    throw new ZEntityNotExistsException($payment_id, PayConfirm::class);
                }
            }
            $this->beginTransaction();

            $payConfirmEntity->submit();

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
                return $e->getMessage();
            }
        }
    }

    /**
     * @desc 提交付款认领
     * @param $claimId
     * @param PayClaim $payClaimEntity
     * @return string|bool|mixed
     * @throws \Exception
     */
    public function submitPayClaim($claimId, $payClaimEntity)
    {
        try
        {
            if(empty($payClaimEntity)) {
                $payClaimEntity = DIService::getRepository(IPayClaimRepository::class)->findByPk($claimId);
                if(empty($payClaimEntity->claim_id)) {
                    throw new ZEntityNotExistsException($claimId, PayClaim::class);
                }
            }

            $this->beginTransaction();

            $payClaimEntity->submit();

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
                return $e->getMessage();
            }
        }
    }
}