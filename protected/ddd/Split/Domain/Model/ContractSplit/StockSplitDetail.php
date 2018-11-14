<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/29 0029
 * Time: 11:00
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\Split\Domain\Model\Stock\StockBill;
use ddd\Split\Domain\Model\TradeGoods;

class StockSplitDetail extends BaseEntity implements IAggregateRoot{

    use StockSplitDetailRepository;

    /**
     * 标识
     * @var   int
     */
    public $split_detail_id;

    /**
     * 合同平移标识
     * @var   int
     */
    public $split_id;

    /**
     * 原出入库单id
     * @var   int
     */
    public $bill_id;

    /**
     * 生成的新出入库单id
     * @var
     */
    public $new_bill_id;

    /**
     * 出入库平移商品明细
     * @var   array
     */
    public $goods_items;

    /**
     * 类型 1:入库拆分明细  2:出库拆分明细
     * @var   array
     */
    public $type;

    public function getId(){
        return $this->split_detail_id;
    }

    /**
     * 设置id
     * @param   $value
     */
    public function setId($value){
        $this->split_detail_id = $value;
    }

    /**
     * 创建对象
     * @param    StockBill $stockBill
     * @param    int $splitId
     * @return   StockSplitDetail
     * @throws   \Exception
     */
    public static function create(StockBill $stockBill, $splitId){
        if(empty($stockBill)){
            ExceptionService::throwArgumentNullException("StockBill对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $entity = new static();
        if(!empty($splitId)){
            $entity->split_id = $splitId;
        }

        if(\Utility::isNotEmpty($stockBill->items)){
            foreach($stockBill->items as $g){
                $detailGoods = TradeGoods::create($g->goods_id);
                $detailGoods->quantity = $g->quantity;
                $entity->addSplitGoodsItem($detailGoods);
            }
        }

        return $entity;
    }

    /**
     * 平移商品明细是否存在
     * @param    int $goodsId
     * @return   boolean
     */
    public function splitGoodsIsExists($goodsId){
        return isset($this->goods_items[$goodsId]);
    }

    /**
     * 添加平移商品明细
     * @param    TradeGoods $stockSplitGoods
     * @return   bool
     * @throws   \Exception
     */
    public function addSplitGoodsItem(TradeGoods $stockSplitGoods){
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
     * 移除平移商品明细
     * @param    int $goodsId
     * @return   bool
     */
    public function remveSplitGoodsItem($goodsId){
        unset($this->goods_items[$goodsId]);

        return true;
    }

    /**
     * 保存出入库拆分生成的出入库单id
     * @param    int $newBillId
     * @throws   \Exception
     */
    public function saveNewBillId($newBillId){
        $this->new_bill_id = $newBillId;
        $this->getStockSplitDetailRepository()->updateNewBillId($this->getId(),$newBillId);
    }

    public function getGoodsQuantities(){
        if(empty($this->goods_items)){
            return [];
        }

        $goodsQuantities = [];
        foreach($this->goods_items as $goods_id => $tradeGoodsEntity){
            $goodsQuantities[$goods_id] = $tradeGoodsEntity->quantity->quantity;
        }

        return $goodsQuantities;
    }
}