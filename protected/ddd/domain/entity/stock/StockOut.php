<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/8 14:36
 * Describe：
 */

namespace ddd\domain\entity\stock;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\value\Attachment;
use ddd\domain\event\stock\StockOutEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\domain\tRepository\stock\StockOutRepository;
use ddd\infrastructure\error\ExceptionService;

class StockOut extends BaseEntity implements IAggregateRoot
{

    /**
     * @var      bigint
     */
    public $out_order_id;

    /**
     * @var      string
     */
    public $code;

    public $status;
    /**
     * @var      bigint
     */
    public $order_id;
    public $store_id;
    /**
     * @var      string 备注
     */
    public $remark;
    /**
     * @var      date
     */
    public $out_date;

    /**
     * @var      bigint
     */
    public $contract_id;

    /**
     * @var      array
     */
    public $items;
    /**
     * @var      array
     */
    public $files=array();

    /**
     * @var      Warehouse
     */
    public $store;

    /**
     * @var      string
     */
    public $transport;

    /**
     * @var      int
     */
    public $transport_mode;

    /**
     * @var      int
     */
    public $partner_id;


    #endregion
    use StockOutRepository;

    function getId()
    {
        return $this->out_order_id;
    }

    function getIdName()
    {
        return "out_order_id";
    }

    function setId($value)
    {
        $this->out_order_id=$value;
    }

    public function rules()
    {
        return array();
    }

    public function init()
    {
        parent::init();
    }

    public function generateId()
    {
        $this->id=\IDService::getStockOutOrderId();
    }


    public static function create(DeliveryOrder $deliveryOrder)
    {
        if(empty($deliveryOrder))
            ExceptionService::throwArgumentNullException("deliveryOrder对象",array('class'=>get_class($self), 'function'=>__FUNCTION__));

        $entity = new StockOut();
        $entity->status = \StockOutOrder::STATUS_SAVED;
        $entity->order_id = $deliveryOrder->order_id;
        if (is_array($deliveryOrder->items) && !empty($deliveryOrder->items))
        {
            foreach ($deliveryOrder->items as $item)
            {
                $out = StockOutItem::create($item);
                $entity->addGoods($out);
            }
        }

        return $entity;
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
     * 添加入库商品明细项
     * @param DeliveryOrderGoods $item
     * @return bool
     * @throws \Exception
     */
    public function addGoods(StockOutItem $item)
    {
        if (empty($item))
        {
            ExceptionService::throwArgumentNullException("DeliveryOrderGoods对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        $goodsId = $item->goods_id;
        /*if ($this->goodsIsExists($goodsId))
        {
            ExceptionService::throwBusinessException(BusinessError::Lading_Goods_Is_Exists, array("goods_id" => $goodsId,));
            //return false;
        }*/
        $this->items[] = $item;

        return true;
    }
    /**
     * 添加附件
     * @param Attachment $item
     * @return bool
     * @throws \Exception
     */
    public function addFilesItems(\ddd\domain\entity\Attachment $item)
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
     */
    public function removeGoods()
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
    public function submit()
    {
        // TODO: implement
    }

    /**
     */
    public function checkBack()
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
    public function generateCode()
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
     */
    public function isCanStockOut()
    {
        // TODO: implement
    }

    /**
     * @desc 入库单审批通过
     * @throws \CException
     */
    public function checkPass()
    {
        EventSubscribeService::bind($this,"onCheckPass", EventSubscribeService::StockOutPassEvent);
        $this->afterPass();
    }

    /**
     * 当入库单审批通过后
     * @throws \CException
     */
    public function afterPass()
    {
        if($this->hasEventHandler('onCheckPass'))
            $this->onCheckPass(new StockOutEvent($this));
    }

    /**
     * 响应入库单审批通过事件
     * @param $event
     * @throws \CException
     */
    public function onCheckPass($event)
    {
        $this->raiseEvent('onCheckPass', $event);
    }

    /**
     * 设为已结算
     * @throws \Exception
     */
    public function setSettledAndSave()
    {
        $this->status = \StockOutOrder::STATUS_SETTLED;
        $this->getStockOutRepository()->setSettled($this);
    }
}