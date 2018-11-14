<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:29
 * Describe：
 */

namespace ddd\application\stock;


use ddd\application\dto\stock\LadingBillDTO;
use ddd\Common\Application\TransactionService;
use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\stock\StockIn;
use ddd\domain\entity\stock\StockOut;
use ddd\domain\iRepository\stock\IStockInRepository;
use ddd\domain\iRepository\stock\IStockOutRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\contract\ContractRepository;

class StockService extends TransactionService
{
    /**
     * 添加提单（入库通知单）
     * @param LadingBillDTO $ladingBill
     * @return array|bool
     * @throws \Exception
     */
    public function addLadingBill(LadingBillDTO $ladingBill)
    {
        if(!$ladingBill->validate())
            return $ladingBill->getErrors();

        $entity=$ladingBill->toEntity();
        if(!$entity->validate())
        {
            return $entity->getErrors();
        }

        $contract=ContractRepository::repository()->findByPk($ladingBill->contract_id);
        if(empty($contract))
            return false;



        try
        {
            $this->beginTransaction();
            $contract=new Contract();
            $contract->addLadingBill();

            //$this->ladingRepository->store($entity);

            $this->commitTransaction();
            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            if($this->isInOutTrans)
                throw $e;
            else
                return false;
        }
    }

    /**
     * 入库单审批通过
     * @param $stockInId
     * @param StockIn $stockInEntity
     * @return array|bool
     * @throws \Exception
     */
    public function passStockIn($stockInId, $stockInEntity)
    {
        try
        {
            if(empty($stockInEntity)) {
                $stockInEntity = DIService::getRepository(IStockInRepository::class)->findByPk($stockInId);
                if(empty($stockInEntity->stock_in_id)) {
                    throw new ZEntityNotExistsException($stockInId, StockIn::class);
                }
            }

            $this->beginTransaction();

            $stockInEntity->checkPass();

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
     * 出库单审批通过
     * @param $stockOutId
     * @param StockOut $stockOutEntity
     * @return array|bool
     * @throws \Exception
     */
    public function passStockOut($stockOutId, $stockOutEntity)
    {
        try
        {
            if(empty($stockOutEntity)) {
                $stockOutEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($stockOutId);
                if(empty($stockOutEntity->out_order_id)) {
                    throw new ZEntityNotExistsException($stockOutId, StockOut::class);
                }
            }

            $this->beginTransaction();

            $stockOutEntity->checkPass();

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