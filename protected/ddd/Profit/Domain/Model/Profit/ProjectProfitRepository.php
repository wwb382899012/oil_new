<?php
/**
 * Desc: 项目利润仓储trait
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Profit;

use ddd\infrastructure\DIService;


trait ProjectProfitRepository
{
    /**
     * @var $projectProfitRepository
     */
    protected $projectProfitRepository;

    /**
     * @desc 获取项目利润仓储
     * @return IProjectProfitRepository
     * @throws \Exception
     */
    protected function getProjectProfitRepository()
    {
        if(empty($this->projectProfitRepository)) {
            $this->projectProfitRepository = DIService::getRepository(IProjectProfitRepository::class);
        }

        return $this->projectProfitRepository;
    }
}