<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/5/30
 * Time: 14:39
 */

namespace ddd\Split\Repository\StockSplit;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Quantity;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyDetailRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitDetail;
use ddd\Split\Domain\Model\TradeGoods;

class StockSplitApplyDetailRepository extends EntityRepository implements IStockSplitApplyDetailRepository{

    /**
     * 获取新的实体对象
     * @return BaseEntity|StockSplitApplyDetail
     * @throws \Exception
     */
    public function getNewEntity(){
        return new StockSplitApplyDetail();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return 'StockSplitApplyDetail';
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return BaseEntity|StockSplitDetail
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = new StockSplitDetail();
        $entity->setAttributes($model->getAttributes());
        $entity->clearGoodsItems();

        if(\Utility::isNotEmpty($model->goods)){
            foreach($model->goods as & $goodsModel){
                $tradeGoodsEntity = new TradeGoods();
                $tradeGoodsEntity->goods_id = $goodsModel->goods_id;
                $tradeGoodsEntity->quantity = new Quantity($goodsModel->quantity,$goodsModel->unit);
                $entity->addGoodsItem($tradeGoodsEntity);
            }
        }

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return StockSplitDetail
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity):StockSplitDetail{
        return $entity;
    }

    public function findByContractId($contractId): StockSplitDetail{
        return $this->find('t.contract_id = :contract_id ',[':contract_id'=>$contractId]);
    }
}