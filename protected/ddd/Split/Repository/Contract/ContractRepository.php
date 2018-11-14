<?php
/**
 * Created by: yu.li
 * Date: 2018/5/30
 * Time: 20:50
 * Desc: ContractRepository
 */

namespace ddd\Split\Repository\Contract;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Quantity;
use ddd\domain\enum\MainEnum;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\Split\Domain\Model\Contract\BuyContract;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\Contract\SellContract;
use ddd\Split\Domain\Model\TradeGoods;

class ContractRepository extends EntityRepository implements IContractRepository{

    public function getNewEntity() {
        return null;
    }

    private function getByContractEntity() {
        return BuyContract::create();
    }

    private function getSellContractEntity() {
        return SellContract::create();
    }

    public function init() {
        $this->with = ['goods','project','partner','corporation'];
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return 'Contract';
    }

    public function dataToEntity($model) {
        if ($model->type == ContractEnum::BUY_CONTRACT) {
            $entity = $this->getByContractEntity();
        } else {
            $entity = $this->getSellContractEntity();
        }
        $entity->setAttributes($model->getAttributes(), false);
        if (is_array($model->goods) && !empty($model->goods)) {
            foreach ($model->goods as $item) {
                $tradeGoods = new TradeGoods();
                $tradeGoods->goods_id = $item->goods_id;
                $tradeGoods->quantity = new Quantity($item->quantity, $item->unit);
                $entity->addGoodsItem($tradeGoods);
            }
        }
        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return IAggregateRoot
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     * @throws \CException
     */
    public function store(IAggregateRoot $entity){
        $id = $entity->getId();
        if(!empty($id)){
            $model = \Contract::model()->with("goods")->findByPk($id);
        }
        if(empty($model)){
            $model = new \Contract();
        }

        if($entity->original_id){
            $originContractModel = \Contract::model()->with("goods")->findByPk($entity->original_id);
            $model->category = $originContractModel->category;
        }

        $values = \Utility::unsetCommonIgnoreAttributes($entity->getAttributes());
        $model->setAttributes($values);

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }

        if(\Utility::isNotEmpty($entity->goods_items)){

            $contractGoodsModels = [];
            foreach($model->goods as & $contractGoodsModel){
                $contractGoodsModels[$contractGoodsModel->goods_id] = $contractGoodsModel;
            }

            foreach($entity->goods_items as & $tradeGoodsEntity){

                if(!isset($contractGoodsModels[$tradeGoodsEntity->goods_id])){
                    $contractGoodsModel = new \ContractGoods();
                }else{
                    $contractGoodsModel = $contractGoodsModels[$tradeGoodsEntity->goods_id];
                    unset($contractGoodsModels[$tradeGoodsEntity->goods_id]);
                }

                $itemValues = $tradeGoodsEntity->getAttributes();
                $itemValues["quantity"] = $tradeGoodsEntity->quantity->quantity;
                $itemValues["quantity_actual"] = $tradeGoodsEntity->quantity->quantity;
                $itemValues["unit"] = $tradeGoodsEntity->quantity->unit;
                unset($itemValues["detail_id"]);
                $contractGoodsModel->setAttributes($itemValues);
                //
                $contractGoodsModel->contract_id = $model->getPrimaryKey();
                $contractGoodsModel->project_id = $model->project_id;

                if(!$contractGoodsModel->save()){
                    throw new ZModelSaveFalseException($contractGoodsModel);
                }
            }

            if(\Utility::isNotEmpty($contractGoodsModels)){
                foreach($contractGoodsModels as & $contractGoodsModel){
                    if(!$contractGoodsModel->delete()){
                        throw new ZModelDeleteFalseException($contractGoodsModel);
                    }
                }
            }
        }

        $entity->setId($model->getPrimaryKey());

        return $entity;
    }

}