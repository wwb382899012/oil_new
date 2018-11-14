<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/29 0029
 * Time: 10:06
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\TradeGoods;

class ContractSplit extends BaseEntity implements IAggregateRoot{
    use ContractSplitRepository;

    //    public $index=0;

    /**
     * 合同平移标识
     * @var   int
     */
    public $split_id;

    public $apply_id;

    /**
     * 合作方
     * @var   int
     */
    public $partner_id;

    /**
     * 合同平移商品明细
     * @var   array
     */
    public $goods_items;

    /**
     * 原合同id
     * @var
     */
    public $contract_id;

    /**
     * 生成的新合同id
     * @var   array
     */
    public $new_contract_id;

    public $status;

    public $status_time = '';

    #region id

    /**
     * 获取id
     * @return int
     */
    public function getId(){
        return $this->split_id;
    }

    #endregion

    /*public function equals(ContractSplit $other)
    {
        if(!empty($this->split_id) && $this->split_id===$other->split_id)
            return true;
        else
        {
            return ($this->index===$other->index);
        }
    }*/

    /**
     * 设置id
     * @param $value
     */
    public function setId($value){
        $this->split_id = $value;
    }

    /**
     * 商品是否已存在
     * @param    int $goodsId
     * @return   boolean
     */
    public function goodsIsExists($goodsId){
        return isset($this->goods_items[$goodsId]);
    }

    /**
     * 创建对象
     * @param    Contract $contract
     * @param    int $partner_id
     * @return   ContractSplit
     * @throws   \Exception
     */
    public static function create(Contract $contract, $partner_id = 0){
        $entity = new static();
        if(!empty($partner_id)){
            $entity->partner_id = $partner_id;
        }

        if(\Utility::isNotEmpty($contract->goods_items)){
            foreach($contract->goods_items as $g){
                $item = TradeGoods::create($g->goods_id);
                $item->quantity = $g->quantity;
                $entity->addGoodsItem($item);
            }
        }

        return $entity;
    }

    /**
     * 添加商品信息
     * @param    TradeGoods $contractSplitGoods
     * @throws   \Exception
     */
    public function addGoodsItem(TradeGoods $contractSplitGoods){
        if(empty($contractSplitGoods)){
            ExceptionService::throwArgumentNullException('TradeGoods对象', array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        if($this->goodsIsExists($contractSplitGoods->goods_id)){
            ExceptionService::throwBusinessException(BusinessError::Contract_Split_Goods_Is_Exists, array('goods_id' => $contractSplitGoods->goods_id));
        }

        $this->goods_items[$contractSplitGoods->goods_id] = $contractSplitGoods;
    }

    /**
     * 移除商品信息
     * @param    int $goodsId
     * @return   bool
     */
    public function removeGoodsItem($goodsId){
        unset($this->goods_items[$goodsId]);

        return true;
    }

    /**
     * 保存合同拆分生成的新合同id
     * @param    int $newContractId
     * @throws   \Exception
     */
    public function saveNewContractId($newContractId){
        $this->new_contract_id = $newContractId;
        $this->getContractSplitRepository()->store($this);
    }

    /**
     * 是否待确认
     * @return bool
     */
    public function isWaitConfirm(){
        return ContractSplitEnum::STATUS_WAIT_CONFIRM == $this->status;
    }

    /**
     * 新合同业务审核通过
     * @throws \Exception
     */
    public function setSplitContractEffective(){
        $this->status = ContractSplitEnum::STATUS_BUSINESS_CHECKED;
        $this->status_time = \Utility::getDateTime();

        $this->getContractSplitRepository()->store($this);
    }

    public function getGoodsIds(): array {
        $goods_ids = [];
        foreach($this->goods_items as $goods_id => & $tmp){
            $goods_ids[$goods_id] = $goods_id;
        }

        return $goods_ids;
    }

    /**
     * 获取拆分合同的商品数量
     * @return array
     */
    public function getGoodsIdQuantityMap(){
        $goods_quantities = [];
        foreach($this->goods_items as $goods_id => & $tradeGoodsEntity){
            $goods_quantities[$goods_id] = $tradeGoodsEntity->quantity->quantity;
        }

        return $goods_quantities;
    }
}
