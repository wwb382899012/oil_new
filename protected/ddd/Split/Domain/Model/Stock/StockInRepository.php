<?php

namespace ddd\Split\Domain\Model\Stock;

use ddd\infrastructure\DIService;

trait StockInRepository{
    /**
     * @var IStockInRepository
     */
    protected $stockInRepository;

    /**
     * 获取采购合同拆分申请仓储
     * @return IStockInRepository|object
     * @throws \Exception
     */
    protected function getStockInRepository(){
        if(empty($this->stockInRepository)){
            $this->stockInRepository = DIService::getRepository(IStockInRepository::class);
        }

        return $this->stockInRepository;
    }
}