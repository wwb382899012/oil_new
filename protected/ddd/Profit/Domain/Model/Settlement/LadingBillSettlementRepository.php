<?php
/**
 * Desc: 入库通知单结算仓储trait
 * User: vector
 * Date: 2018/8/28
 * Time: 11:13
 */

namespace ddd\Profit\Domain\Model\Settlement;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Settlement\ILadingBillSettlementRepository;


trait LadingBillSettlementRepository
{
    /**
     * @var IContractRepository
     */
    protected $ladingBillSettlementRepository;

    /**
     * @desc 获取入库通知单结算仓储
     * @return IContractRepository
     * @throws \Exception
     */
    protected function getLadingBillSettlementRepository()
    {
        if(empty($this->ladingBillSettlementRepository)) {
            $this->ladingBillSettlementRepository = DIService::getRepository(ILadingBillSettlementRepository::class);
        }

        return $this->ladingBillSettlementRepository;
    }
}