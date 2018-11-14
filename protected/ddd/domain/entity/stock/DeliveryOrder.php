<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 15:23
 * Describe：
 */

namespace ddd\domain\entity\stock;



use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\contractSettlement\DeliveryOrderSettlementItem;
use ddd\domain\event\stock\DeliveryOrderEvent;
use ddd\domain\event\stock\DeliveryOrderRejectEvent;
use ddd\domain\event\stock\DeliveryOrderSettledEvent;
use ddd\domain\event\stock\DeliveryOrderSettledRejectEvent;
use ddd\domain\event\stock\DeliveryOrderSettlingEvent;
use ddd\domain\event\stock\DeliveryOrderSubmitEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\domain\iRepository\stock\IDeliveryOrderRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\Utility;

class DeliveryOrder extends BaseEntity implements IAggregateRoot
{

    /**
     * 提交事件
     */
    const EVENT_AFTER_SUBMIT="onAfterSubmit";
    /**
     * 提交事件
     */
    const EVENT_AFTER_BACK="onAfterBack";
    /**
     * 提交事件
     */
    const EVENT_AFTER_PASS="onAfterPass";

    /**
     * 结算驳回事件
     */
    const EVENT_AFTER_SETTLED_BACK="onAfterSettledBack";
    /**
     * 开始结算事件
     */
    const EVENT_AFTER_SETTLING="onAfterSettling";

    /**
     * 结算完成事件
     */
    const EVENT_AFTER_SETTLED="onAfterSettled";

    /**
     * @var      bigint
     */
    public $order_id;

    /**
     * @var      string
     */
    public $code;

    /**
     * @var      bigint
     */
    public $contract_id;

    /**
     * @var      int
     */
    public $currency;

    /**
     * @var      int
     */
    public $corporation_id;

    /**
     * @var      date
     */
    public $delivery_date;
    /**
     * @var      string 备注
     */
    public $remark;

    /**
     * @var      Partner
     */
    public $partner_id;

    /**
     * 发货单商品明细信息
     *        array(goodsId=>DeliveryOrderGoods)
     * @var      array
     */
    public $items=array();

    public $distribute_items = array();

    public $files=array();
    
    public $settleItems = array();

    /**
     * 1：经仓
     * 2：直调
     * @var      int
     */
    public $type;

    /**
     * @var      int
     */
    public $status;

    /**
     * @var      datetime
     */
    public $status_time;

    /**
     * @var      string
     */
    public $transportation;

    /**
     * @var      int
     */
    public $transport_mode;



    /**
     * @var IDeliveryOrderRepository
     */
    protected $repository;

    public function init()
    {
        $this->getRepository();
        parent::init();

        $this->status = \DeliveryOrder::STATUS_NEW;
    }

    /**
     * 获取仓储
     * @return IDeliveryOrderRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(IDeliveryOrderRepository::class);
        }
        return $this->repository;
    }

    public function getId()
    {
        return $this->order_id;
    }

    public function getIdName()
    {
        return "order_id";
    }

    function setId($value)
    {
        $this->order_id=$value;
    }

    public function generateId()
    {
        $this->order_id = \IDService::getDeliveryOrderId();
    }

    /**
     * 生成编码
     */
    public function generateCode()
    {
        $this->code = "";
    }

    /*public function isCanSettle()
    {
        if(empty($this->order_id))
            return false;
        return DeliveryOrderSettlementService::isCanDeliveryOrderSettle($this->order_id);
        // return true;
    }*/

    /**
     * 创建发货单对象
     * @return DeliveryOrder
     * @throws \Exception
     */
    public static function create()
    {
        $entity = new DeliveryOrder();
        $entity->generateId();
        $entity->generateCode();
        $entity->delivery_date = \Utility::getDate();
        $entity->status = \DeliveryOrder::STATUS_NEW;
        

        return $entity;
    }

    /**
     * 添加商品明细项
     * @param DeliveryOrderGoods $item
     * @return bool
     * @throws \Exception
     */
    public function addGoods(DeliveryOrderGoods $item)
    {
        // TODO: implement
        if (empty($item))
        {
            ExceptionService::throwArgumentNullException("DeliveryOrderGoods对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        $goodsId = $item->goods_id;
        if ($this->goodsIsExists($goodsId))
        {
            ExceptionService::throwBusinessException(BusinessError::Delivery_Order_Goods_Is_Exists, array("goods_id" => $goodsId,));
            //return false;
        }
        $this->items[$goodsId] = $item;

        return true;

    }
    /**
     * 添加附件
     * @param Attachment $item
     * @return bool
     * @throws \Exception
     */
    public function addFilesItems(Attachment $item)
    {
       
        if (empty($item))
        {
            ExceptionService::throwArgumentNullException("DeliveryOrderSettlementItem对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }
        
        $id = $item->id;
        $this->files[$id] = $item;
        return true;
        
    }
    /**
     * 添加结算商品明细项
     * @param DeliveryOrderSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addSettleItems(DeliveryOrderSettlementItem $item)
    {
        if (empty($item))
        {
            ExceptionService::throwArgumentNullException("DeliveryOrderSettlementItem对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }
        $goodsId = $item->goods_id;
        $this->settleItems[$goodsId] = $item;
        return true;
    }

    /**
     * 判断当前商品项是否已经存在
     * @param $goodsId
     * @return bool
     */
    public function goodsIsExists($goodsId)
    {
        return isset($this->items[$goodsId]);
    }

    /**
     * 移除发货的商品项
     * @param    int $goodsId
     * @return   boolean
     */
    public function removeItem($goodsId)
    {
        // TODO: implement
    }

    /**
     */
    public function trash()
    {
        // TODO: implement
    }

    /**
     */
    public function done()
    {
        // TODO: implement
    }

    /**
     */
    public function isCanEdit()
    {
        // TODO: implement
    }


    /**
     * 发货单提交
     * @throws \CException
     */
    public function submit()
    {
        $this->status = DeliveryOrderStatus::STATUS_SUBMIT;
        $this->repository->submit($this);

        $this->afterSubmit();
    }

    /**
     * 发货单提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SUBMIT, EventSubscribeService::DeliveryOrderSubmitEvent);
        if($this->hasEventHandler(static::EVENT_AFTER_SUBMIT))
            $this->onAfterSubmit(new DeliveryOrderSubmitEvent($this));
    }

    /**
     * 响应发货单提交事件
     * @param  $event
     * @throws \CException
     */
    public function onAfterSubmit($event)
    {
        $this->raiseEvent(static::EVENT_AFTER_SUBMIT, $event);
    }

    /**
     * 发货单审核驳回
     * @throws \CException
     */
    public function checkBack()
    {
        $this->status = DeliveryOrderStatus::STATUS_BACK;
        $this->repository->back($this);

        $this->afterCheckBack();
    }

    /**
     * 发货单驳回后
     * @throws \CException
     */
    public function afterCheckBack()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_BACK, EventSubscribeService::DeliveryOrderBackEvent);
        if($this->hasEventHandler(static::EVENT_AFTER_BACK))
            $this->onAfterCheckBack(new DeliveryOrderRejectEvent($this));
    }

    /**
     * 响应发货单驳回事件
     * @param  $event
     * @throws \CException
     */
    public function onAfterCheckBack($event)
    {
        $this->raiseEvent(static::EVENT_AFTER_BACK, $event);
    }

    /**
     * 发货单审核通过
     * @throws \CException
     */
    public function checkPass()
    {
        $this->status = DeliveryOrderStatus::STATUS_PASS;
        $this->repository->pass($this);

        $this->afterCheckPass();
    }

    /**
     * 发货单审核通过后
     * @throws \CException
     */
    public function afterCheckPass()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_PASS, EventSubscribeService::DeliveryOrderEvent);
        if($this->hasEventHandler(static::EVENT_AFTER_PASS))
            $this->onAfterCheckPass(new DeliveryOrderEvent($this));
    }

    /**
     * 响应发货单审核通过事件
     * @param  $event
     * @throws \CException
     */
    public function onAfterCheckPass($event)
    {
        $this->raiseEvent(static::EVENT_AFTER_PASS, $event);
    }

    #region 结算及完结相关

    /**
     * 设为结算驳回
     * @throws \Exception
     */
    public function setSettledBackAndSave()
    {
        $this->status=DeliveryOrderStatus::STATUS_SETTLE_BACK;
        $this->status_time=Utility::getNow();
        $this->repository->setSettledBack($this);

        $this->afterSettledBack();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettledBack()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SETTLED_BACK,EventSubscribeService::DeliveryOrderSettledBackEvent);
        $event=new DeliveryOrderSettledRejectEvent();
        if($this->hasEventHandler(static::EVENT_AFTER_SETTLED_BACK))
            $this->raiseEvent(static::EVENT_AFTER_SETTLED_BACK, $event);
    }

    /**
     * 设为结算中
     * @throws \Exception
     */
    public function setOnSettlingAndSave()
    {
        $this->status=DeliveryOrderStatus::STATUS_SETTLE_SUBMIT;
        $this->status_time=Utility::getNow();
        $this->repository->setOnSettling($this);

        $this->afterSettling();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettling()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SETTLING,EventSubscribeService::DeliveryOrderSettlingEvent);
        $event=new DeliveryOrderSettlingEvent();
        if($this->hasEventHandler(static::EVENT_AFTER_SETTLING))
            $this->raiseEvent(static::EVENT_AFTER_SETTLING, $event);
    }

    /**
     * 设为结算完成
     * @throws \Exception
     */
    public function setSettledAndSave()
    {
        $this->status=DeliveryOrderStatus::STATUS_SETTLE_PASS;
        $this->status_time=Utility::getNow();
        $this->repository->setSettled($this);

        $this->afterSettled();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettled()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SETTLED,EventSubscribeService::DeliveryOrderSettledEvent);
        $event=new DeliveryOrderSettledEvent();
        if($this->hasEventHandler(static::EVENT_AFTER_SETTLED))
            $this->raiseEvent(static::EVENT_AFTER_SETTLED, $event);
    }

    #endregion
}