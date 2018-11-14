<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/5/29
 * Time: 16:26
 */

namespace ddd\Split\Domain\Model\StockSplit;

use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\Split\Domain\Model\TradeGoods;


/**
 * 出入库平移明细
 * Class StockSplitDetail
 * @package ddd\Split\Domain\Model\Stock
 */
class StockSplitDetail extends BaseEntity{

    #region property

    public $detail_id = 0;

    /**
     * 合同id
     * @var   int
     */
    public $contract_id = 0;

    /**
     * 出入库单id
     * @var   int
     */
    public $bill_id = 0;

    public $type;

    public $remark = '';

    /**
     * 新的出入库单id
     * @var int
     */
    public $new_bill_id = 0;

    /**
     * 出入库平移商品明细
     * @var   array
     */
    protected $goods_items = [];

    #endregion

    /**
     * 创建对象
     * @param BaseEntity $stockBill
     * @param $contractId
     * @return StockSplitDetail
     * @throws \Exception
     */
    public static function create(BaseEntity $stockBill, $contractId){
        $entity = new static();
        $entity->bill_id = $stockBill->bill_id;
        $entity->contract_id = $contractId;
        return $entity;
    }

    public function getGoodsItems(){
        return $this->goods_items;
    }

    public function clearGoodsItems(){
        $this->goods_items = [];
    }

    /**
     * 添加平移商品明细
     * @param TradeGoods $stockSplitGoods
     * @return bool
     * @throws \Exception
     */
    public function addGoodsItem(TradeGoods $stockSplitGoods){
        if(empty($stockSplitGoods)){
            ExceptionService::throwArgumentNullException('TradeGoods对象', array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        if($this->splitGoodsIsExists($stockSplitGoods->goods_id)){
            ExceptionService::throwBusinessException(BusinessError::Stock_Split_Goods_Is_Exists, array('goods_id' => $stockSplitGoods->goods_id));
        }

        $this->goods_items[$stockSplitGoods->goods_id] = $stockSplitGoods;

        return true;
    }

    /**
     * 商品明细是否存在
     * @param    int $goodsId
     * @return   boolean
     */
    public function splitGoodsIsExists($goodsId){
        return isset($this->goods_items[$goodsId]);
    }

    /**
     * 移除平移商品明细
     * @param $goodsId
     * @return bool
     */
    public function removeGoodsItem($goodsId){
        unset($this->goods_items[$goodsId]);
        return true;
    }
}