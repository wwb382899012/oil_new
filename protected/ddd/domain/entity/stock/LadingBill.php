<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 15:23
 * Describe：
 */

namespace ddd\domain\entity\stock;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\contract\TradeGoods;
use ddd\domain\entity\contractSettlement\LadingBillSettlementItem;
use ddd\domain\entity\value\Quantity;
use ddd\domain\event\stock\LadingBillSettledEvent;
use ddd\domain\event\stock\LadingBillSettledRejectEvent;
use ddd\domain\event\stock\LadingBillSettlingEvent;
use ddd\domain\event\stock\LadingBillSubmitEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\domain\iRepository\stock\ILadingBillRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\Utility;

class LadingBill extends BaseEntity implements IAggregateRoot
{
    /**
     * 提交事件
     */
    const EVENT_AFTER_SUBMIT="onAfterSubmit";

    /**
     * 结算驳回事件
     */
    const EVENT_AFTER_SETTLED_BACK="onAfterSettledBack";
    /**
     * 开始结算事件
     */
    const EVENT_AFTER_SETTLING="onAfterSettling";

    /**
     * 合同结算完成事件
     */
    const EVENT_AFTER_SETTLED="onAfterSettled";

    /**
     * @var      bigint
     */
    public $id;

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
     * @var      date
     */
    public $lading_date;

    /**
     * @var      Partner
     */
    public $partner_id;

    /**
     * 入库通知单商品明细信息
     *        array(goods_id=>LadingBillGoods)
     * @var      array
     */
    public $items=array();
    /**
     * 入库通知单附件
     *        
     * @var      array
     */
    public $files=array();
    public $settleItems;
    /**
     * 1：经仓
     * 2：直调
     * @var      int
     */
    public $type=1;

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
     * @var      string
     */
    public $remark;

    /**
     * @var ILadingBillRepository
     */
    protected $repository;

    public function init()
    {
        $this->getRepository();
        parent::init();
        $this->status=\StockNotice::STATUS_NEW;

    }

    /**
     * 获取仓储
     * @return ILadingBillRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(ILadingBillRepository::class);
        }
        return $this->repository;
    }
    
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    public function rules()
    {
        return array(
            array("code","required"),
            array("contract_id",'numerical', 'integerOnly'=> 'true','min'=>1),
        );
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "id";
    }

    function setId($value)
    {
        $this->id=$value;
    }


    public function generateId()
    {
        $this->id=\IDService::getStockBatchId();
    }

    /*public function customAttributeNames()
    {
        return \StockNotice::model()->attributeNames();
    }*/


    #region FactoryMethod

    /**
     * 创建提单对象
     * @param Contract $contract
     * @return LadingBill
     * @throws \Exception
     */
    public static function create(Contract $contract)
    {
        if(empty($contract))
            ExceptionService::throwArgumentNullException("Contract对象",array('class'=>get_called_class(), 'function'=>__FUNCTION__));
        
        if(!$contract->isCanLading())
            ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Lading,array("contract_code"=>$contract->contract_code));
        
        $entity=new LadingBill();
        $entity->contract_id=$contract->contract_id;
        $entity->generateId();
        $entity->generateCode();
        $entity->lading_date=Utility::getDete();
        $entity->status=\StockNotice::STATUS_NEW;
        if(is_array($contract->goodsItems) && !empty($contract->goodsItems)) {
            foreach ($contract->goodsItems as $g)
            {
                $item = LadingBillGoods::create($g->goods_id);
                $quantity   = $g->quantity - $g->quantity_actual;
                $quantity   = $quantity<0?0:$quantity;
                $item->quantity     = new Quantity($quantity,$g->unit);
                $tradeGoods = TradeGoods::create($item->contract_id, $g);
                $unit_store = $tradeGoods->getStockSubUnit();
                $item->quantitySub  = new Quantity($quantity,$unit_store);
                $entity->addItem($item);
            }
        }
       
        return $entity;

    }


    #endregion


    /**
     * 添加提单商品明细项
     * @param LadingBillGoods $item
     * @return bool
     * @throws \Exception
     */
    public function addItem(LadingBillGoods $item)
    {
        // TODO: implement
        if(empty($item))
            ExceptionService::throwArgumentNullException("LadingBillGoods对象",array('class'=>get_class($this), 'function'=>__FUNCTION__));

        $goodsId=$item->goods_id;
        if($this->goodsIsExists($goodsId))
        {
            ExceptionService::throwBusinessException(BusinessError::Lading_Goods_Is_Exists,
                                                     array("goods_id"=>$goodsId,));
            //return false;
        }
        $this->items[$goodsId]=$item;
        return true;

    }

    /**
     * 添加结算商品明细项
     * @param LadingBillSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addSettleItems(LadingBillSettlementItem $item)
    {
        if (empty($item))
        {
            ExceptionService::throwArgumentNullException("LadingBillSettlementItem对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        $goodsId = $item->goods_id;

        $this->settleItems[$goodsId] = $item;

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
     * 判断当前商品项是否已经存在
     * @param $goodsId
     * @return bool
     */
    public function goodsIsExists($goodsId)
    {
        return isset($this->items[$goodsId]);
    }

    /**
     * 移除入库的商品项
     * @param    int $goodsId
     * @return   boolean
     */
    public function removeItem($goodsId)
    {
        // TODO: implement
        unset($this->items[$goodsId]);
        return true;
    }



    /**
     * 生成编码
     */
    public function generateCode()
    {
        // TODO: implement
        $this->code="";
    }

    /**
     * 是否可以编辑
     * @return bool
     */
    public function isCanEdit()
    {
        return $this->stauts<LadingBillStatus::STATUS_SUBMIT;
    }

    /**
     * 提单提交
     * @throws \CException
     */
    public function submit()
    {
        $this->status = LadingBillStatus::STATUS_SUBMIT;
        $this->repository->submit($this);

        $this->afterSubmit();
    }

    /**
     * 提单提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SUBMIT, EventSubscribeService::LadingBillSubmitEvent);
        if($this->hasEventHandler(static::EVENT_AFTER_SUBMIT))
            $this->onAfterSubmit(new LadingBillSubmitEvent($this));
    }

    /**
     * 响应提单提交事件
     * @param  $event
     * @throws \CException
     */
    public function onAfterSubmit($event)
    {
        $this->raiseEvent(static::EVENT_AFTER_SUBMIT, $event);
    }

    #region 结算及完结相关

    /**
     * 设为结算驳回
     * @throws \Exception
     */
    public function setSettledBackAndSave()
    {
        $this->status=LadingBillStatus::STATUS_SETTLE_BACK;
        $this->status_time=Utility::getNow();
        $this->repository->setSettledBack($this);

        $this->afterSettledBack();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettledBack()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SETTLED_BACK,EventSubscribeService::LadingBillSettledBackEvent);
        $event=new LadingBillSettledRejectEvent();
        if($this->hasEventHandler(static::EVENT_AFTER_SETTLED_BACK))
            $this->raiseEvent(static::EVENT_AFTER_SETTLED_BACK, $event);
    }

    /**
     * 设为结算中
     * @throws \Exception
     */
    public function setOnSettlingAndSave()
    {
        $this->status=LadingBillStatus::STATUS_SETTLE_SUBMIT;
        $this->status_time=Utility::getNow();
        $this->repository->setOnSettling($this);

        $this->afterSettling();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettling()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SETTLING,EventSubscribeService::LadingBillSettlingEvent);
        $event=new LadingBillSettlingEvent();
        if($this->hasEventHandler(static::EVENT_AFTER_SETTLING))
            $this->raiseEvent(static::EVENT_AFTER_SETTLING, $event);
    }

    /**
     * 设为结算完成
     * @throws \Exception
     */
    public function setSettledAndSave()
    {
        $this->status=LadingBillStatus::STATUS_SETTLED;
        $this->status_time=Utility::getNow();
        $this->repository->setSettled($this);

        $this->afterSettled();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettled()
    {
        EventSubscribeService::bind($this,static::EVENT_AFTER_SETTLED,EventSubscribeService::LadingBillSettledEvent);
        $event=new LadingBillSettledEvent();
        if($this->hasEventHandler(static::EVENT_AFTER_SETTLED))
            $this->raiseEvent(static::EVENT_AFTER_SETTLED, $event);
    }


    #endregion



}