<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：
 */

namespace ddd\application\receipt;


use ddd\Common\Application\TransactionService;
use ddd\domain\entity\receipt\ReceiptClaim;
use ddd\domain\iRepository\receipt\IPayClaimRepository;
use ddd\domain\iRepository\receipt\IReceiptClaimRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\receipt\ReceiptClaimRepository;

class ReceiptService extends TransactionService
{

    protected $receiptClaimRepository;

    public function __construct()
    {
        $this->receiptClaimRepository = new ReceiptClaimRepository();
    }

    /**
     * @desc 提交收款认领
     * @param $receive_id
     * @param ReceiptClaim $receiptClaimEntity
     * @return string|bool|mixed
     * @throws \Exception
     */
    public function submitReceiptClaim($receive_id, $receiptClaimEntity)
    {
        try
        {
            if(empty($receiptClaimEntity))
            {
                $receiptClaimEntity = DIService::getRepository(IReceiptClaimRepository::class)->findByPk($receive_id);

                if (empty($receiptClaimEntity->receive_id))
                {
                    throw new ZEntityNotExistsException($receive_id, ReceiptClaim::class);
                }
            }


            $this->beginTransaction();

            $receiptClaimEntity->submit();

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