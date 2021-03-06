<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/28 10:56
 * Describe：
 *      合同商品信息，该实体不是聚合于合同的合同交易商品，是一个独立的聚合根
 */

namespace ddd\domain\entity\contract;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\domain\iRepository\contract\ITradeGoodsRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZException;

class TradeGoods extends BaseEntity implements IAggregateRoot
{

    #region property

    /**
     * 标识
     * @var   bigint
     */
    public $detail_id;

    /**
     * 合同id
     * @var   bigint
     */
    public $contract_id;

    /**
     * 溢短装比
     * @var   float
     */
    public $more_or_less_rate;

    /**
     * 商品
     * @var   int
     */
    public $goods_id;

    /**
     * 合同单位
     * @var   int
     */
    public $unit;

    /**
     * 库存单位
     * @var   int
     */
    public $unit_store;

    /**
     * 锁价单位
     * @var   int
     */
    public $unit_price;

    /**
     * 计价参考标的
     * @var   int
     */
    public $refer_target;

    /**
     * 单价
     * @var   int
     */
    public $price;
    /**
     * 总价
     * @var   int
     */
    public $amount;
    /**
     * 数量
     * @var   Quantity
     */
    public $quantity;

    /**
     * 库存数量
     * @var   Quantity
     */
    public $quantity_stock;

    /**
     * 入库数量
     * @var   Quantity
     */
    public $quantity_stock_in;

    /**
     * 出库数量
     * @var   Quantity
     */
    public $quantity_stock_out;

    /**
     * 1：按合同
     * 2：按入库通知单
     * 锁价方式
     * @var   int
     */
    public $lock_type;

    #endregion

    /**
     * @var ITradeGoodsRepository
     */
    protected $repository;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        //$this->repository=new ContractGoodsRepository();
    }

    /**
     * 获取合同商品仓储
     * @return ITradeGoodsRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(ITradeGoodsRepository::class);
        }
        return $this->repository;
    }


    public function rules()
    {
        return array(
            array('quantity', 'numerical', 'min'=>0, 'max'=>9999999999999999),
        );
    }


    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->detail_id;
    }

    function setId($value)
    {
        // TODO: Implement setId() method.
        $this->detail_id=$value;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "detail_id";
    }

    /**
     * 创建对象的工厂方法
     * @param int $contractId
     * @param ContractGoods|null $contractGoods
     * @return TradeGoods
     * @throws \Exception
     */
    public static function create($contractId=0, ContractGoods $contractGoods=null)
    {
        $params=[];
        if(!empty($contractGoods))
        {
            $params=$contractGoods->getAttributes();
        }
        $params['contract_id']=$contractId;

        return new static($params);
    }

    /**
     * 获取库存第二单位
     * @return   int
     */
    public function getStockSubUnit()
    {
        if(!empty($this->unit_store))
            return $this->unit_store;
        if(!empty($this->unit_price) && $this->unit_price!=$this->unit)
            return $this->unit_price;
        else
            return $this->unit;

    }

    /**
     * 获取锁价单位
     * @return   int
     */
    public function getPriceLockUnit()
    {
        if(!empty($this->unit_price))
            return $this->unit_price;
        if(!empty($this->unit_store) && $this->unit_store!=$this->unit)
            return $this->unit_store;
        else
            return $this->unit;
    }

    /**
     * 获取当前商品的所有计量单位信息
     */
    protected function getUnits()
    {
        $units=[$this->unit=>$this->unit];
        if(!empty($this->unit_price))
            $units[$this->unit_price]=$this->unit_price;
        if(!empty($this->unit_store))
            $units[$this->unit_store]=$this->unit_store;

        return $units;
    }

    /**
     * 设置库存第二单位
     * @param    int $unit
     * @throws ZException
     */
    public function setAndSaveStockSubUnit($unit)
    {
        // TODO: implement
        if(!$this->isCanSetStockSubUnit())
        {
            throw new ZException("库存第二单位不能设置");
        }

        $units=$this->getUnits();
        if(count($units)==1 || key_exists($unit,$units))
        {
            $this->unit_stock=$unit;
            $this->repository->saveUnitStore($this);
            //$this->repository->saveUnitStore($this);
        }
        else
            throw new ZException("单位不在可选单位内");


    }

    /**
     * 是否可以设置库存第二单位
     * @return bool
     */
    public function isCanSetStockSubUnit()
    {
        return empty($this->unit_store);
    }

    /**
     * 设置锁价单位
     * @param    int $unit
     * @throws ZException
     */
    public function setAndSavePriceLockUnit($unit)
    {
        // TODO: implement
        if(!$this->isCanSetPriceLockUnit())
        {
            throw new ZException("锁价单位不能设置");
        }

        $units=$this->getUnits();
        if(count($units)==1 || key_exists($unit,$units))
        {
            $this->unit_price=$unit;
            $this->repository->saveUnitPrice($this);
        }
        else
            throw new ZException("单位不在可选单位内");
    }

    /**
     * 是否可以设置锁价单位
     * @return bool
     */
    public function isCanSetPriceLockUnit()
    {
        return empty($this->unit_price);
    }

    /**
     * 设置锁价方式
     * @param    int $lockType
     * @throws ZException
     */
    public function setAndSaveLockType($lockType)
    {
        // TODO: implement
        if(!empty($this->lock_type))
            throw new ZException("锁价方式已经确定，不能修改");
        $this->lock_type=$lockType;
        $this->repository->saveLockType($this);
    }

    /**
     * 增加库存量
     * @param    int $quantity
     */
    public function addStock($quantity)
    {
        // TODO: implement
        $this->quantity_stock->quantity+=$quantity;
    }

    /**
     * 增加库存量并保存
     * @param $quantity
     */
    public function addAndSaveStock($quantity)
    {
        $this->addStock($quantity);
        $this->repository->saveStockQuantity($this,$quantity);
    }

    /**
     * 增加入库数量
     * @param    int $quantity
     */
    public function addStockIn($quantity)
    {
        // TODO: implement
        $this->quantity_stock_in->quantity+=$quantity;
        $this->repository->saveStockInQuantity($this,$quantity);
    }

    /**
     * 增加入库数量并保存
     * @param    int $quantity
     */
    public function addAndSaveStockIn($quantity)
    {
        // TODO: implement
        $this->addStockIn($quantity);
        $this->repository->saveStockInQuantity($this,$quantity);
    }

    /**
     * 增加出库数量
     * @param    int $quantity
     */
    public function addStockOut($quantity)
    {
        // TODO: implement
        $this->quantity_stock_out->quantity+=$quantity;
        $this->repository->saveStockOutQuantity($this,$quantity);
    }

    /**
     * 增加出库数量并保存
     * @param    int $quantity
     */
    public function addAndSaveStockOut($quantity)
    {
        // TODO: implement
        $this->addStockOut($quantity);
        $this->repository->saveStockOutQuantity($this,$quantity);
    }

}