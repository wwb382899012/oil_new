<?php

namespace ddd\Split\Domain\Model\Stock;

use ddd\infrastructure\DIService;

trait StockOutRepository{
    /**
     * @var IStockOutRepository
     */
    protected $stockOutRepository;

    /**
     * 获取采购合同拆分申请仓储
     * @return IStockOutRepository|object
     * @throws \Exception
     */
    protected function getStockOutRepository(){
        if(empty($this->stockOutRepository)){
            $this->stockOutRepository = DIService::getRepository(IStockOutRepository::class);
        }

        return $this->stockOutRepository;
    }
}