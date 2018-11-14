<?php

namespace ddd\Profit\Application;

use ddd\Common\Application\TransactionService;
use ddd\Common\Domain\Value\Quantity;
use ddd\Contract\Domain\Model\Project\IProjectRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Profit\CorporationProfit;
use ddd\Profit\Domain\Model\Profit\CorporationProfitService;
use ddd\Profit\Domain\Model\Profit\DeliveryOrderProfit;
use ddd\Profit\Domain\Model\Profit\DeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Profit\DeliveryOrderProfitService;
use ddd\Profit\Domain\Model\Profit\ICorporationProfitRepository;
use ddd\Profit\Domain\Model\Profit\IDeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Profit\IProjectProfitRepository;
use ddd\Profit\Domain\Model\Profit\ProjectProfit;
use ddd\Profit\Domain\Model\Profit\ProjectProfitService;
use ddd\Profit\Domain\Model\Profit\SellContractProfitService;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCost;
use ddd\Profit\Domain\Model\Stock\DeliveryOrder;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
use ddd\Profit\Domain\Model\Profit\ISellContractProfitRepository;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\Profit\Domain\Model\Profit\SellContractProfit;
use ddd\Profit\Domain\Model\Stock\StockNotice;
use ddd\Profit\Domain\Model\Stock\StockNoticeCost;
use ddd\Profit\Repository\ProjectRepository;
use ddd\Profit\Domain\Model\Stock\IStockNoticeCostRepository;
use ddd\Profit\Domain\Model\Stock\IStockNoticeRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeCostRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeRepository;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCostRepository;
use ddd\Profit\Domain\Model\Stock\DeliveryOrderRepository;

/**
 * 结算利润报表 服务
 * Class ProfitService
 * @package ddd\Profit\Application
 */
class ProfitService extends TransactionService
{

    use DeliveryOrderProfitRepository;
    use StockNoticeCostRepository;
    use StockNoticeRepository;
    use BuyGoodsCostRepository;
    use DeliveryOrderRepository;

    public function __construct()
    {

    }

    /**
     * @name:createDeliveryOrderProfit
     * @desc: 创建发货单利润 对象
     * @param:* @param $deliveryOrderId
     * @throw: * @throws ZEntityNotExistsException
     * @throws ZException
     * @throws \Exception
    @return:DeliveryOrderProfit
     */
    public function createDeliveryOrderProfit($deliveryOrderId)
    {

        if(empty($deliveryOrderId))
            throw new ZException("deliveryOrderId 参数为空");

        $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($deliveryOrderId);
        if(empty($deliveryOrder))
            throw  new ZEntityNotExistsException($deliveryOrderId,DeliveryOrder::class);

        //$profit=$this->getDeliveryOrderProfitRepository()->findByOrderId($deliveryOrderId);

        try{
            $this->beginTransaction();

            $service = new DeliveryOrderProfitService();
            $profit = $service->createDeliveryOrderProfit($deliveryOrder, true); //持久化在领域服务中限制

            $this->commitTransaction();

            return $profit;
        }
        catch(\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }

    /**
     * @name:createStockNoticeCost
     * @desc: 创建入库通知单成本 对象
     * @param:* @param $batchId
     * @throw: * @throws ZEntityNotExistsException
     * @throws ZException
     * @throws \Exception
    @return:void
     */
    public function createStockNoticeCost($batchId)
    {

        if(empty($batchId))
            throw new ZException("batchId 参数为空");

        $stockNotice =  DIService::getRepository(IStockNoticeRepository::class)->findByPk($batchId);
        if(empty($stockNotice))
            throw  new ZEntityNotExistsException($batchId,StockNotice::class);

        try{
            $this->beginTransaction();

            $stockNotice = DIService::getRepository(IStockNoticeRepository::class)->findByPk($batchId);

            $service = new StockNoticeCost();
            $stockNoticeCost = $service->create($stockNotice);

            $this->getStockNoticeCostRepository()->store($stockNoticeCost);

            $this->commitTransaction();
            return true;
        }
        catch(\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }


    /**
     * @name:addContractProfit
     * @desc: 生成合同利润
     * @param:* @param $orderId
     * @throw: * @throws \Exception
     * @return:string
     */
    public function addContractProfit($orderId){
        if(empty($orderId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($orderId);
        if(empty($deliveryOrder))
            return \BusinessError::outputError(\OilError::$DELIVERY_ORDER_NOT_EXIST,array('order_id'=>$orderId));
        $contract =DIService::getRepository(IContractRepository::class)->findByPk($deliveryOrder->contract_id);

        $service = new SellContractProfitService();
        $service->createSellContractProfit($contract, true); //持久化在领域服务中限制

    }

    /**
     * @name:addProjectProfit
     * @desc: 生成项目利润
     * @param:* @param $contractId
     * @throw: * @throws \Exception
     * @return:string
     */
    public function addProjectProfit($contractId){
        if(empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $contract =DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        $project = ProjectRepository::repository()->findByPk($contract->project_id);

        $service = new ProjectProfitService();
        $service->createProjectProfit($project, true); //持久化在领域服务中限制
    }

    /**
     * @name:addProjectProfitByProjectId
     * @desc: 生成项目利润
     * @param:* @param $project_id
     * @throw:
     * @return:void
     */
    public function addProjectProfitByProjectId($project_id) {
        if(empty($project_id))
            return ;
        $projectEntity = ProjectRepository::repository()->findByPk($project_id);
        ProjectProfitService::service()->createProjectProfit($projectEntity,true);
    }
    /**
     * @name:addCorporationProfit
     * @desc: 生成交易主体利润
     * @param:* @param $projectId
     * @throw: * @throws \Exception
     * @return:string
     */
    public function addCorporationProfit($projectId){
        if(empty($projectId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $project =ProjectRepository::repository()->findByPk($projectId);

        $service = new CorporationProfitService();
        $service->createCorporationProfit($project->corporation_id, true); //持久化在领域服务中限制
    }

    /**
     * @name:addCorporationProfitByCorporationId
     * @desc: 生成交易主体利润
     * @param:* @param $corporation_id
     * @throw:
     * @return:string
     */
    public function addCorporationProfitByCorporationId($corporation_id){
        if(empty($corporation_id))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);

        CorporationProfitService::service()->createCorporationProfit($corporation_id,true);
    }

    /**
     * @name:addBuyGoodsCostByBatchId
     * @desc: 生成采购商品成本
     * @param:* @param $batchId
     * @throw: * @throws \Exception
     * @return:string
     */
    public function addBuyGoodsCostByBatchId($batchId){
        if(empty($batchId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);

        //$deliveryOrderList = $this->getDeliveryOrderRepository()->findByBatchId($batchId);
        $deliveryOrderList = \ddd\Profit\Repository\DeliveryOrderRepository::repository()->findByBatchId($batchId);
        if(!empty($deliveryOrderList)){
            foreach($deliveryOrderList as & $item){
                $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($item->order_id);
                $service = new BuyGoodsCost();
                $buyGoodsCost=$service->create($deliveryOrder);
                if(!empty($buyGoodsCost)){
                    foreach ($buyGoodsCost as & $value) {
                        $this->getBuyGoodsCostRepository()->store($value);
                    }
                }

            }
        }

        \AMQPService::publishProfit($batchId,null); //发起事件
    }

    /**
     * @name:addBuyGoodsCostByContractId
     * @desc: 生成采购商品成本
     * @param:* @param $contractId
     * @throw: * @throws \Exception
     * @return:string
     */
    public function addBuyGoodsCostByContractId($contractId)
    {
        if (empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $stockNoticeList = $this->getStockNoticeRepository()->findByContractId($contractId);
        if (!empty($stockNoticeList)) {
            foreach ($stockNoticeList as $stockNotice) {

                //$deliveryOrderList = $this->getDeliveryOrderRepository()->findByBatchId($stockNotice->batch_id);
                $deliveryOrderList = \ddd\Profit\Repository\DeliveryOrderRepository::repository()->findByBatchId($stockNotice->batch_id);
                if (!empty($deliveryOrderList)) {
                    foreach ($deliveryOrderList as & $item) {
                        $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($item->order_id);
                        $service = new BuyGoodsCost();
                        $buyGoodsCost = $service->create($deliveryOrder);
                        if (!empty($buyGoodsCost)) {
                            foreach ($buyGoodsCost as & $value) {
                                $this->getBuyGoodsCostRepository()->store($value);
                            }
                        }

                    }
                }
            }
        }
        \AMQPService::publishProfit(null,$contractId); //发起事件
    }

    /**
     * @name:addProfitByBatchId
     * @desc: 生成利润
     * @param:* @param $batchId
     * @throw: * @throws \Exception
     * @return:string
     */
    public function addProfitByBatchId($batchId){
        if(empty($batchId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);

        $deliveryOrderList = \ddd\Profit\Repository\DeliveryOrderRepository::repository()->findByBatchId($batchId);

        if(!empty($deliveryOrderList)){
            foreach($deliveryOrderList as & $item){
                $service = new DeliveryOrderProfitService();
                $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($item->order_id);
                $profit = $service->createDeliveryOrderProfit($deliveryOrder, true); //持久化在领域服务中限制

            }
        }

    }

    /**
     * @name:addProfitByContractId
     * @desc: 生成采购商品成本
     * @param:* @param $contractId
     * @throw: * @throws \Exception
     * @return:string
     */
    public function addProfitByContractId($contractId)
    {
        if (empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $stockNoticeList = $this->getStockNoticeRepository()->findByContractId($contractId);
        if (!empty($stockNoticeList)) {
            foreach ($stockNoticeList as $stockNotice) {

                //$deliveryOrderList = $this->getDeliveryOrderRepository()->findByBatchId($stockNotice->batch_id);
                $deliveryOrderList = \ddd\Profit\Repository\DeliveryOrderRepository::repository()->findByBatchId($stockNotice->batch_id);
                if (!empty($deliveryOrderList)) {
                    foreach ($deliveryOrderList as & $item) {
                        $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($item->order_id);
                        $service = new DeliveryOrderProfitService();
                        $profit = $service->createDeliveryOrderProfit($deliveryOrder, true); //持久化在领域服务中限制

                    }
                }
            }
        }

    }

}