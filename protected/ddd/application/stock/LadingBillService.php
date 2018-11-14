<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:29
 * Describe：
 */

namespace ddd\application\stock;

use ddd\application\dto\stock\LadingBillDTO;
use ddd\Common\Application\TransactionService;
use ddd\repository\stock\LadingBillRepository;

class LadingBillService extends TransactionService
{
    /**
     * 获取入库通知单
     * @param $batch_id 入库通知单id
     * @return array|bool
     * @throws \Exception
     */
    public function getLadingBill($batch_id)
    {
        $batchInBatchEntity = LadingBillRepository::repository()->findByPk($batch_id);
        if(!empty($batchInBatchEntity)){
            $LadindBillDTO = new LadingBillDTO();
            $LadindBillDTO->fromEntity($batchInBatchEntity);
            return $LadindBillDTO->getAttributes();
        }
        else 
          return array();
           
    }
    /**
     * 入库通知单是否可以结算
     * @param $batch_id 入库通知单id
     * @return array|bool
     * @throws \Exception
     */
    public function isCanSettle($batch_id)
    {
        $LadingBillEntity = LadingBillRepository::repository()->findByPk($batch_id);
        if(!empty($LadingBillEntity)){
            $service = new \ddd\domain\service\stock\LadingBillService();
            $isCanSettle =  $service->isCanSettle($LadingBillEntity);
            if(is_bool(isCanSettle)&&$isCanSettle)
                return true;
            else
                return $isCanSettle;
        }
        else {
            return \BusinessError::outputError(\OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batch_id));

        }

    }
}