<?php
/**
 * Created by vector.
 * DateTime: 2018/3/29 11:56
 * Describe：发货单结算
 */

namespace ddd\domain\entity\settlement;


use ddd\domain\entity\stock\DeliveryOrder;
use ddd\domain\entity\value\Currency;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementEvent;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementRejectEvent;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementSubmitEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\domain\iRepository\contractSettlement\IDeliveryOrderSettlementRepository;
use ddd\domain\service\stock\DeliveryOrderService;
use ddd\infrastructure\DIService;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\repository\contract\ContractRepository;

class DeliveryOrderSettlement extends Settlement
{

    /**
    * @var      bigint
    */
    public $order_id;

    /**
     * @var IDeliveryOrderSettlementRepository
     */
    protected $repository;

    public function init()
    {
        $this->getRepository();
        parent::init();

    }

    /**
     * 获取仓储
     * @return IDeliveryOrderSettlementRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(IDeliveryOrderSettlementRepository::class);
        }
        return $this->repository;
    }

    /**
     * 创建对象
     * @param DeliveryOrder $deliveryOrder
     * @return DeliveryOrderSettlement
     * @throws \Exception
     */
    public static function create(DeliveryOrder $deliveryOrder)
    {
        if(empty($deliveryOrder))
            throw new ZException("DeliveryOrder对象不存在");

        $contract = ContractRepository::repository()->findByPk($deliveryOrder->contract_id);
        if($contract->settle_type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT)
            throw new ZException(BusinessError::Now_Settle_Mode_Is_Sale_Contract_Settle,array("contract_code"=>$contract->contract_code));

        $isBoolAndMsg = DeliveryOrderService::service()->isCanSettle($deliveryOrder);
        if($isBoolAndMsg !== true)
            throw new ZException($isBoolAndMsg);
        
        $entity = new DeliveryOrderSettlement();
        $entity->order_id        =  $deliveryOrder->order_id;
        $entity->contract_id     =  $deliveryOrder->contract_id;
        $entity->settle_currency =  Currency::getCurrency($deliveryOrder->currency);
        $entity->status          =  SettlementStatus::STATUS_NEW;

        // $entity->status_time=Utility::getDateTime();
        if(is_array($deliveryOrder->items) && !empty($deliveryOrder->items)) {
            foreach ($deliveryOrder->items as $g)
            {
                $item = GoodsSettlement::create($g->goods_id);
                $item->relation_id     = $deliveryOrder->order_id;
                $item->bill_quantity   = $g->out_quantity;
                $item->settle_quantity = $g->out_quantity;
                $item->loss_quantity   = new Quantity(0, $g->out_quantity->unit);

                $billItem = BillSettlementItem::create($g->goods_id);
                $billItem->item_id         = $item->item_id;
                $billItem->bill_id         = $deliveryOrder->order_id;
                $billItem->bill_quantity   = $g->out_quantity;
                $billItem->settle_quantity = $g->out_quantity;
                $billItem->loss_quantity   = new Quantity(0, $g->out_quantity->unit);
                $item->addBillSettlementItem($orderItem);

                $entity->addGoodsSettlement($item);
            }
        }

        return $entity;
    }


    /**
     * 发货单结算提交
     * @throws \CException
     */
    public function submit()
    {
        $this->status = SettlementStatus::STATUS_SUBMIT;
        $this->repository->submit($this);

        $this->afterSubmit();
    }

    /**
     * 发货单结算提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        EventSubscribeService::bind($this,"onAfterSubmit", EventSubscribeService::DeliveryOrderSettlementSubmitEvent);
        if($this->hasEventHandler('onAfterSubmit'))
            $this->onAfterSubmit(new DeliveryOrderSettlementSubmitEvent($this));
    }

    /**
     * 响应发货单提交事件
     * @param  $event
     * @throws \CException 
     */
    public function onAfterSubmit($event)
    {
        $this->raiseEvent('onAfterSubmit', $event);
    }

    public function checkPass()
    {
        $this->status = SettlementStatus::STATUS_PASS;
        $this->status_time = Utility::getNow();

        $this->repository->setSettled($this);
//        $this->repository->updateContractSettlementAmount($this);

        $this->afterCheckPass();
    }


    public function afterCheckPass()
    {
        EventSubscribeService::bind($this,"onAfterCheckPass", EventSubscribeService::DeliveryOrderSettlementPassEvent);
        if($this->hasEventHandler('onAfterCheckPass'))
            $this->onAfterCheckPass(new DeliveryOrderSettlementEvent($this));
    }

    public function onAfterCheckPass($event)
    {
        $this->raiseEvent('onAfterCheckPass', $event);
    }


    public function checkBack()
    {
        $this->status = SettlementStatus::STATUS_BACK;
        $this->repository->back($this);

        $this->afterCheckBack();
    }

    public function afterCheckBack()
    {
        EventSubscribeService::bind($this,"onAfterCheckBack", EventSubscribeService::DeliveryOrderSettlementBackEvent);
        if($this->hasEventHandler('onAfterCheckBack'))
            $this->onAfterCheckBack(new DeliveryOrderSettlementRejectEvent($this));
    }

    public function onAfterCheckBack($event)
    {
        $this->raiseEvent('onAfterCheckBack', $event);
    }


    /**
     * 生成编号
     */
    public function generateId()
    {
         $this->settle_id=\IDService::getDeliverySettlementId();
    }

    /**
     * 生成编码
     */
    public function generateCode()
    {
        $this->code=\IDService::getDeliverySettlementCode();
    }

}