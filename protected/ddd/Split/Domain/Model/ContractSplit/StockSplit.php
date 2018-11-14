<?php
/**
 * Desc: 出入库拆分
 * User: susiehuang
 * Date: 2018/5/29 0029
 * Time: 10:27
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\Split\Domain\Model\Stock\StockBill;
use ddd\Split\Domain\Model\TradeGoods;

class StockSplit extends BaseEntity{

    /**
     * 出入库单id
     * @var   int
     */
    public $bill_id;

    /**
     * 出入库单编号
     * @var   string
     */
    public $bill_code;

    public $status;

    /**
     * 拆分明细项
     * @var   array
     */
    protected $details;

    /**
     * 剩余商品信息
     * @var   array
     */
    protected $balance_goods = [];

    public function getId(){
        return $this->bill_id;
    }

    public function setId($value){
        $this->bill_id = $value;
    }

    /**
     * 创建对象
     * @param    StockBill $stockBill
     * @param    int $itemKey
     * @return   StockSplit
     * @throws   \Exception
     */
    public static function create(StockBill $stockBill, $itemKey){
        if(empty($stockBill)){
            ExceptionService::throwArgumentNullException("StockBill对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        if(!$stockBill->isCanSplit()){
            ExceptionService::throwBusinessException(BusinessError::Stock_Bill_Cannot_Split, array('bill_code' => $stockBill->bill_code));
        }

        $entity = new static();
        $entity->bill_id = $stockBill->bill_id;
        $entity->bill_code = $stockBill->bill_code;
        $entity->initBalanceGoods($stockBill);

        if(\Utility::isNotEmpty($stockBill->items)){
            $splitDetail = StockSplitDetail::create($stockBill);
            $entity->addSplitDetail($splitDetail, $itemKey);
        }

        return $entity;
    }

    /**
     * 初始化可拆分商品数量
     * @param StockBill $stockBill
     */
    public function initBalanceGoods(StockBill $stockBill){
        $this->balance_goods = [];
        if(is_array($stockBill->items)){
            foreach($stockBill->items as $g){
                $this->balance_goods[$g->goods_id] = $g->quantity->quantity;
            }
        }
    }

    /**
     * 增加合同平移的出入库平移明细
     * @param $itemKey
     * @param TradeGoods $goods
     * @param bool $checkBalanceGoods
     * @throws ZException
     */
    public function addStockSplitGoods($itemKey, TradeGoods $goods, $checkBalanceGoods = true){
        if(!key_exists($goods->goods_id, $this->balance_goods)){
            throw new ZException("出入库拆分中不存在商品".$goods->goods_id);
        }

        if(!isset($this->details[$itemKey])){
            $this->addSplitDetailWithContractSplit($itemKey);
        }

        if($checkBalanceGoods && ($goods->quantity->quantity > $this->balance_goods[$goods->goods_id])){
            throw new ZException("商品".$goods->goods_id."拆分数量超出");
        }

        $this->details[$itemKey]->addSplitGoodsItem($goods);
        $this->balance_goods[$goods->goods_id] -= $goods->quantity->quantity;

    }

    /**
     * 添加合同平移下的出入库平移明细
     * @param    $itemKey
     * @throws   \Exception
     */
    protected function addSplitDetailWithContractSplit($itemKey){
        $detail = new StockSplitDetail();
        //$detail->split_id = $itemKey;

        $this->addSplitDetail($detail, $itemKey);
    }

    /**
     * 添加出入库平移明细
     * @param StockSplitDetail $stockSplitDetail
     * @param $itemKey
     * @param bool $checkBalanceGoods
     * @throws ZException
     */
    public function addSplitDetail(StockSplitDetail $stockSplitDetail, $itemKey, $checkBalanceGoods = true){
        if(\Utility::isNotEmpty($stockSplitDetail)){
            ExceptionService::throwArgumentNullException("StockSplitDetail对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        if(is_array($stockSplitDetail->goods_items)){
            if($checkBalanceGoods){
                foreach($stockSplitDetail->goods_items as $g){
                    if($this->balance_goods[$g->goods_id] < $g->quantity->quantity){
                        throw new ZException("商品".$g->goods_id."拆分数量超出");
                    }
                }
            }

            //商品数量减去拆分数量
            foreach($stockSplitDetail->goods_items as $g){
                $this->balance_goods[$g->goods_id] -= $g->quantity->quantity;
            }
        }

        $this->details[$itemKey] = $stockSplitDetail;
    }

    /**
     * 清除合同平移下的出入库平移明细
     * @param $itemKey
     */
    public function removeSplitDetail($itemKey){
        if(empty($this->details[$itemKey])){
            return;
        }

        //加回出入库拆分数量
        foreach($this->details[$itemKey]->goods_items as $g){
            $this->balance_goods[$g->goods_id] += $g->quantity->quantity;
        }

        unset($this->details[$itemKey]);
    }

    /**
     * 获取出入库拆分明细项
     * @param $splitId
     * @return mixed|null
     */
    public function getStockSplitDetail($splitId){
        return $this->details[$splitId] ?? null;
    }

    public function clearDetails(){
        $this->details = [];
    }

    public function getDetails(){
        return $this->details;
    }

    /**
     * 是否有效的出入库拆分
     * @return bool
     */
    public function isEffective(){
        return ContractSplitApplyEnum::STATUS_SPLIT == $this->status;
    }
}