<?php
/**
 * Desc: 交易主体利润仓储trait
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Profit;

use ddd\infrastructure\DIService;


trait CorporationProfitRepository
{
    /**
     * @var $corporationProfitRepository
     */
    protected $corporationProfitRepository;

    /**
     * @desc 获取交易主体利润仓储
     * @return IProjectProfitRepository
     * @throws \Exception
     */
    protected function getCorporationProfitRepository()
    {
        if(empty($this->corporationProfitRepository)) {
            $this->corporationProfitRepository = DIService::getRepository(ICorporationProfitRepository::class);
        }

        return $this->corporationProfitRepository;
    }
}