<?php

namespace ddd\Split\Domain\Service\StockSplit;

use ddd\Common\Domain\BaseService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\Split\Domain\Model\StockSplit\StockSplitApply;

/**
 * 出入库拆分领域服务
 * Class StockSplitService
 * @package ddd\Split\Domain\Service\StockSplit
 */
class StockSplitService extends BaseService{

    public function save(StockSplitApply $stockSplitEntity){
        if (!$stockSplitEntity->isCanEdit()){
            ExceptionService::throwBusinessException(BusinessError::Stock_Split_Not_Allow_Edit);
        }

        $stockSplitEntity->save();
    }

    public function submit(StockSplitApply $stockSplitEntity){
        if (!$stockSplitEntity->isCanSubmit()){
            ExceptionService::throwBusinessException(BusinessError::Stock_Split_Not_Allow_Submit);
        }

        $stockSplitEntity->submit();
    }

    public function checkBack(StockSplitApply $stockSplitEntity){
        if (!$stockSplitEntity->isCanBack()){
            ExceptionService::throwBusinessException(BusinessError::Stock_Split_Not_Allow_Reject);
        }

        $stockSplitEntity->checkBack();
    }

    public function checkPass(StockSplitApply $stockSplitEntity){
        if (!$stockSplitEntity->isCanPass()){
            ExceptionService::throwBusinessException(BusinessError::Stock_Split_Not_Allow_Approve);
        }

        $stockSplitEntity->checkPass();
    }
}