<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:29
 * Describe：
 */

namespace ddd\application\stock;


use ddd\Common\Application\TransactionService;
use ddd\application\dto\stock\StockInDTO;
use ddd\domain\entity\stock\StockIn;
use ddd\domain\iRepository\stock\IStockInRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\stock\StockInRepository;

class StockInService extends TransactionService
{
    /**
     * 入库单审批通过
     * @param $stockInId
     * @param StockIn $stockInEntity
     * @return array|bool
     * @throws \Exception
     */
    public function passStockIn($stockInId, StockIn $stockInEntity = null)
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
     * 根据入库通知单id获取入库单列表
     * @param $batch_id 入库通知单id
     * @return array|bool
     * @throws \Exception
     */
    public function getStockInByBatchId($batch_id)
    {
        $batchInEntity =  StockInRepository::repository()->findAllByBatchId($batch_id);
        $stockIn=array();
        if(!empty($batchInEntity)){
            foreach ($batchInEntity as $key=>$value){
                $stockInDTO = new StockInDTO();
                $stockInDTO -> fromEntity($value);
                $stockIn[]=$stockInDTO -> getAttributes();
            }
        }
        return $stockIn;
            
    }
}