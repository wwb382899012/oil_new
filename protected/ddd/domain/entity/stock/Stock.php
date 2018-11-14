<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/8 14:28
 * Describe：
 */

namespace ddd\domain\entity\stock;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\value\Quantity;
use ddd\domain\iRepository\stock\IStockRepository;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;


class Stock extends BaseEntity implements IAggregateRoot
{

    #region property

    /**
     * @var      int
     */
    public $stock_id;

    /**
     * @var      int
     */
    public $contract_id=0;

    /**
     * @var      int
     */
    public $stock_in_id=0;

    /**
     * @var      int
     */
    public $goods_id=0;

    /**
     * @var      date
     */
    public $stock_in_date;

    /**
     * @var      date
     */
    public $stock_done_date;

    /**
     * @var      int
     */
    public $store_id=0;

    /**
     * @var      Quantity
     */
    public $quantity;

    /**
     * @var      Quantity
     */
    public $quantity_balance;

    /**
     * @var      Quantity
     */
    public $quantity_frozen;

    /**
     * @var      Quantity
     */
    public $quantity_out;

    /**
     * @var      Quantity
     */
    public $quantity_loss;

    /**
     * @var      int
     */
    public $status;

    #endregion

    /**
     * 库存仓储
     * @var IStockRepository
     */
    protected $repository;


    function getId()
    {
        // TODO: Implement getId() method.
        return $this->stock_id;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "stock_id";
    }

    function setId($value)
    {
        $this->stock_id=$value;
    }


    /**
     * 创建库存对象
     * @param StockIn $stockIn
     * @param StockInItem $stockInItem
     * @return Stock
     * @throws \Exception
     */
    public static function create(StockIn $stockIn,StockInItem $stockInItem)
    {
        $obj=new Stock();
        $obj->stock_id=$stockInItem->id;
        $obj->stock_in_id=$stockIn->getId();
        $obj->goods_id=$stockInItem->goods_id;
        $obj->quantity=$stockInItem->quantity;
        $obj->store_id=$stockIn->store_id;
        $obj->contract_id=$stockIn->contract_id;
        $obj->stock_in_id=$stockIn->getId();
        $obj->stock_in_date=Utility::getToday();
        $obj->status=StockStatus::AVAILABLE;

        $obj->initStockQuantity();

        return $obj;
    }

    /**
     * 初始化库存数量，只在第一次创建库存时执行
     */
    private function initStockQuantity()
    {
        $this->quantity_balance=$this->quantity;
        $quantity=new Quantity(0,$this->quantity->unit);
        $this->quantity_frozen=$quantity;
        $this->quantity_loss=$quantity;
        $this->quantity_out=$quantity;
    }

    /**
     *  获取Stock仓储
     * @return IStockRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if(empty($this->repository))
            $this->repository=Utility::getDIContainer()->get(IStockRepository::class);
        return $this->repository;
    }

    /**
     * 冻结库存，需冻结库存不得大于可用库存
     * @param $quantity
     * @throws \Exception
     */
    public function freezeAndSave($quantity)
    {
        // TODO: implement
        if($this->quantity_balance->quantity<$quantity)
             throw new ZException(BusinessError::Stock_Quantity_Balance_Not_Enough,["balance"=>$this->quantity_balance->quantity,"quantity"=>$quantity]);

        $repository=$this->getRepository();
        $repository->freeze($this,$quantity);
        $this->quantity_balance->subtract($quantity);
        $this->quantity_frozen->add($quantity);
    }

    /**
     * @param $quantity
     * @throws \Exception
     */
    public function unFreezeAndSave($quantity)
    {
        // TODO: implement
        if($this->quantity_frozen->quantity<$quantity)
            throw new ZException(BusinessError::Stock_Frozen_Quantity_Not_Enough,["frozen"=>$this->quantity_frozen->quantity,"quantity"=>$quantity]);

        $repository=$this->getRepository();
        $repository->unFreeze($this,$quantity);

        $this->quantity_frozen->subtract($quantity);
        $this->quantity_balance->add($quantity);
    }

    /**
     * 出库
     * @param $quantity
     * @throws \Exception
     */
    public function outAndSave($quantity)
    {
        // TODO: implement
        if($this->quantity_balance->quantity<$quantity)
            throw new ZException(BusinessError::Stock_Quantity_Balance_Not_Enough,["balance"=>$this->quantity_balance->quantity,"quantity"=>$quantity]);

        $repository=$this->getRepository();
        $repository->out($this,$quantity);
        $this->quantity_balance->subtract($quantity);
        $this->quantity_out->add($quantity);
    }

    /**
     * 退库
     * @param $quantity
     * @throws \Exception
     */
    public function refundAndSave($quantity)
    {
        if($this->quantity_out->quantity<$quantity)
            throw new ZException(BusinessError::Stock_Quantity_Out_Not_Enough,["out"=>$this->quantity_out->quantity,"quantity"=>$quantity]);

        $repository=$this->getRepository();
        $repository->refund($this,$quantity);
        $this->quantity_out->subtract($quantity);
        $this->quantity_balance->add($quantity);
    }

    /**
     * @return   float
     */
    public function getAvailableQuantity()
    {
        // TODO: implement
        return $this->quantity_balance->quantity;
    }

    /**
     * 获取实际库存量
     * @return   float
     */
    public function getQuantity()
    {
        // TODO: implement
        return $this->quantity_balance->quantity+$this->quantity_frozen->quantity;
    }

    /**
     * @return   float
     */
    public function getFrozenQuantity()
    {
        // TODO: implement
        return $this->quantity_frozen->quantity;
    }

    /**
     * @return   float
     */
    public function getOutQuantity()
    {
        // TODO: implement
        return $this->quantity_out->quantity;
    }

    /**
     * @return   float
     */
    public function getLossQuantity()
    {
        // TODO: implement
        return $this->quantity_loss->quantity;
    }

    /**
     */
    protected function addLog()
    {
        // TODO: implement
    }

    /**
     */
    protected function addFreezeLog()
    {
        // TODO: implement
    }

}