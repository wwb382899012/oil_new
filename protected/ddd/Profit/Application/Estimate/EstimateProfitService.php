<?php

namespace ddd\Profit\Application\Estimate;

use ddd\Common\Application\TransactionService;
use ddd\Contract\Domain\Model\Project\IProjectRepository;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Contract\IContractRepository;
use ddd\Profit\Domain\Contract\ContractRepository;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfit;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfitService;
use ddd\Profit\Domain\EstimateProfit\EstimateCorporationProfitService;
use ddd\Profit\Domain\EstimateProfit\EstimateProjectProfitService;
use ddd\Profit\Domain\Event\IEventRepository;
use ddd\Profit\Domain\Service\EventService;
use ddd\Profit\Repository\CorporationRepository;
use ddd\Profit\Repository\ProjectRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZException;

/**
 * 预估利润报表 服务
 * Class ProfitService
 * @package ddd\Profit\Application
 */
class EstimateProfitService extends TransactionService
{

    use ContractRepository;

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * [createEstimateContractProfit 创建预估合同利润]
     * @param [bigint] $contractId [合同id]
     * @return [bool]
     */
    public function createEstimateContractProfit($contractId)
    {
        $contract=$this->getContractRepository()->findByContractId($contractId);
        if(empty($contract))
            throw new ZEntityNotExistsException($contractId,Contract::class);

        try
        {

            $this->beginTransaction();

            EstimateContractProfitService::service()->createEstimateContractProfit($contract,true);

            //mq事件
            \AMQPService::publishSellContractBusinessCheckPass($contract->relation_contract_id);

            \AMQPService::publishEstimateContractProfit($contract->project_id);

            EventService::service()->store($contract->contract_id, \Event::ESTIMATE_CONTRACT_PROFIT_EVENT, \Event::EstimateContractProfitEvent);

            $this->commitTransaction();

            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }


    /**
     * @name:createEstimateProjectProfit
     * @desc:  创建预估项目利润
     * @param:* @param $projectId
     * @throw: * @throws ZException
     * @return:void
     */
    public function createEstimateProjectProfit($projectId)
    {

        try{
            $this->beginTransaction();

            $projectEntity = ProjectRepository::repository()->findByPk($projectId);

            $estimateProjectProfitService = new EstimateProjectProfitService();
            $estimateProjectProfitService->createProjectProfit($projectEntity,true);

            \AMQPService::publishEstimateProjectProfit($projectEntity->corporation_id);

            $this->commitTransaction();

            return ;
        }
        catch(\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }

    /**
     * @name:createEstimateCorporationProfit
     * @desc: 创建交易主体利润
     * @param:* @param $corporationId
     * @throw: * @throws ZException
     * @return:void
     */
    public function createEstimateCorporationProfit($corporationId)
    {

        try{
            $this->beginTransaction();

            $corporationEntity = CorporationRepository::repository()->findByPk($corporationId);
            $estimateCorporationProfitService = new EstimateCorporationProfitService();
            $estimateCorporationProfitService->createCorporationProfit($corporationEntity,true);

            $this->commitTransaction();

            return ;
        }
        catch(\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }


}