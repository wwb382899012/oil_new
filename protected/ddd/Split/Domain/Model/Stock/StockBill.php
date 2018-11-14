<?php
/**
 * Created by youyi000.
 * DateTime: 2018/6/1 15:20
 * Describe：
 */

namespace ddd\Split\Domain\Model\Stock;

use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\ExceptionService;
use ddd\Split\Domain\Model\StockSplit\StockSplitApplyRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use ddd\Split\Domain\Model\TradeGoods;

abstract class StockBill extends BaseEntity
{

    #region property

    /**
     * ID
     * @var   int
     */
    public $bill_id;

    /**
     * 编号
     * @var   string
     */
    public $bill_code;

    /**
     * 商品明细
     * @var   array
     */
    public $items = [];

    /**
     * 是否虚拟单，是否拆分生成，0：原始，1：拆分后的
     * @var
     */
    public $is_virtual = false;

    /**
     * 拆分状态,0:默认,1:拆分中,2:已经拆分
     * @var int
     */
    public $split_status = 0;

    /**
     * @var int
     * 原入库单/出库单ID
     */
    public $original_id = 0;

    public $remark;

    /**
     * 状态
     * @var   int
     */
    public $status = 0;

    /**
     * 创建日期
     * @var   datetime
     */
    public $create_time;

    /**
     * 更新日期
     * @var   datetime
     */
    public $update_time;

    /**
     * 出入库类型
     */
    protected $type;

    #endregion

    use StockSplitApplyRepository;

    abstract function getType();

    public function getId() {
        return $this->bill_id;
    }

    public function setId($value) {
        $this->bill_id = $value;
    }

    /**
     * 添加商品
     * @param TradeGoods $tradeGoods
     * @return bool
     * @throws \Exception
     */
    public function addGoodsItem(TradeGoods $tradeGoods) {
        if (empty($tradeGoods)) {
            ExceptionService::throwArgumentNullException('TradeGoods对象', array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        if ($this->goodsItemIsExists($tradeGoods->goods_id)) {
            ExceptionService::throwBusinessException(BusinessError::Stock_Split_Goods_Is_Exists, array('goods_id' => $tradeGoods->goods_id));
        }

        $this->items[$tradeGoods->goods_id] = $tradeGoods;

        return true;
    }

    public function goodsItemIsExists() {
        return isset($this->items[$tradeGoods->goods_id]);
    }

    /**
     * 移除商品
     * @param $goodsId
     */
    public function removeGoodsItem($goodsId) {
        unset($this->items[$goodsId]);
    }

    public function clearGoodsItems() {
        $this->items = [];
    }

    /**
     * 创建对象
     */
    public function create() {
        $entity = new static();
        $entity->type = $this->getType();
        return $entity;
    }

    /**
     * 保存
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    abstract function save($persistent = true);

    public function getBalanceGoods() {
        return $this->items;
    }

    /**
     * 是否有审核中的拆分申请，子类需要重写该方法
     * @return bool
     * @throws \Exception
     */
    public function hasSplitApplyInChecking() {
        $StockSplitEntityArray = $this->getStockSplitApplyRepository()->findByBillId($this->bill_id);
        if (\Utility::isNotEmpty($StockSplitEntityArray)) {
            foreach ($StockSplitEntityArray as & $StockSplitEntity) {
                if ($StockSplitEntity->status == StockSplitEnum::STATUS_SUBMIT) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 设置正在被拆分中
     */
    public function setIsSplitting() {
        $this->split_status = StockSplitEnum::SPLIT_STATUS_ONGOING;
    }

    /**
     * 取消正在被拆分中
     */
    public function cancelIsSplitting() {
        $this->split_status = StockSplitEnum::SPLIT_STATUS_DEFAULT;
    }

    /**
     * 设置正在已被拆分
     */
    public function setHasBeenSplit() {
        $this->split_status = StockSplitEnum::SPLIT_STATUS_END;
    }

    /**
     * 1.原出、入库单，未拆分过的，可以平移
     * 2.虚拟单，未拆分过的，可以平移
     * 是否可平移
     * @return bool
     */
    public function isCanSplit(): bool {
        //没有平移过的才可以平移
        if ($this->split_status != StockSplitEnum::SPLIT_STATUS_DEFAULT) {
            return false;
        }

        //1.原出、入库单，未拆分过的，可以平移
        //2.虚拟单，未拆分过的，可以平移
        //判断可拆分数量是否为0
        foreach ($this->items as & $item) {
            if ($item->quantity->quantity > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * 原出、入库单的虚拟出、入库单只要未拆完，可再拆
     * @deprecated
     * @return bool
     */
    public function isCanSplitReentry(){
        //未平移过的，可以平移
        $condition_one = ($this->split_status == StockSplitEnum::SPLIT_STATUS_DEFAULT);
        //虚拟单，拆分过的，可以继续平移
        $condition_two = ($this->split_status == StockSplitEnum::SPLIT_STATUS_END) && ($this->original_id > 0);

        if ($condition_one || $condition_two) {
            //判断可拆分数量是否为0
            foreach ($this->items as & $item) {
                if ($item->quantity->quantity > 0) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * 是否 待审核和待提交
     */
    abstract public function isOnChecking();


    /**
     * 入库单/出库单 是否已经被拆分
     * @return bool
     */
    public function isSplit() {
        return $this->split_status > StockSplitEnum::SPLIT_STATUS_END;
    }


}