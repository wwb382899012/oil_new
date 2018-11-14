<?php

namespace ddd\Split\Domain\Model\StockSplit;

use ddd\infrastructure\DIService;

trait StockSplitApplyRepository{
    /**
     * @var IStockSplitApplyRepository
     */
    protected $stockSplitApplyRepository;

    /**
     * 获取采购合同拆分申请仓储
     * @return IStockSplitApplyRepository|object
     * @throws \Exception
     */
    public function getStockSplitApplyRepository(){
        if(empty($this->stockSplitApplyRepository)){
            $this->stockSplitApplyRepository = DIService::getRepository(IStockSplitApplyRepository::class);
        }

        return $this->stockSplitApplyRepository;
    }
}