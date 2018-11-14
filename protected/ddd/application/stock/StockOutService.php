<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:29
 * Describe：
 */

namespace ddd\application\stock;

use ddd\Common\Application\TransactionService;
use ddd\application\dto\stock\StockOutDTO;
use ddd\domain\entity\stock\StockOut;
use ddd\domain\iRepository\stock\IStockOutRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\stock\StockOutRepository;

class StockOutService extends TransactionService
{
    /**
     * 出库单审批通过
     * @param $stockOutId
     * @param StockOut $stockOutEntity
     * @return array|bool
     * @throws \Exception
     */
    public function passStockOut($stockOutId, StockOut $stockOutEntity=null)
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
    /**
     * 根据发货单id获取出库单列表
     * @param $order_id 发货单id
     * @return array|bool
     * @throws \Exception
     */
    public function getStockOutByOrderId($order_id)
    {
        $stockOutEntity =  StockOutRepository::repository()->findAllByOrderId($order_id);
        $stockOut=array();
        if(!empty($stockOutEntity)){
            foreach ($stockOutEntity as $key=>$value){
                $StockOutDTO = new StockOutDTO();
                $StockOutDTO -> fromEntity($value);
                $stockOut[]=$StockOutDTO -> getAttributes();
            }
        }
        return $stockOut;
        
    }
}