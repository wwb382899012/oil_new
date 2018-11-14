<?php
/**
 * Created by: wwb
 * Date: 2018/6/1
 * Time: 17:45
 * Desc: StockInRepository
 */

namespace ddd\Profit\Repository\Stock;


use ConstantMap;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\Profit\Domain\Model\Stock\IStockNoticeCostRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeCost;
use ddd\Profit\Domain\Model\Stock\StockNotice;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Profit\Domain\Service\UnitService;
use ddd\Profit\Domain\Model\Stock\IStockNoticeRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeItem;
use ddd\Profit\Domain\Service\ThirdPriceService;

class StockNoticeRepository extends EntityRepository implements IStockNoticeRepository
{


    public function init() {
        $this->with = array();
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|StockOut
     * @throws \Exception
     */
    public function getNewEntity() {
        return new StockNotice();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return "StockNotice";
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model) {

        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        if($model->contract->settle_type == SettlementMode::LADING_BILL_MODE_SETTLEMENT){//按发货单结算
            $entity->settle_status = $model->ladingSettlement[0]->status;
        }else{//按合同结算
            $entity->settle_status = $model->contract->contractSettlement->status;
        }
        $entity->price_type = $model->contract->price_type;

        if (is_array($model->details)) {
            foreach ($model->details as & $data) {
                    $item = new StockNoticeItem();
                    $item->batch_id = $data->batch_id;
                    $item->goods_id = $data->goods_id;
                    if($model->contract->settle_type == SettlementMode::LADING_BILL_MODE_SETTLEMENT) {//按发货单结算
                        //结算币种为人民币时，price有值，price_cny没有值；结算币种为美元时,price、price_cny都有值
                        $settle_price = empty($data->contractSettlementGoods->price_cny)?$data->contractSettlementGoods->price:$data->contractSettlementGoods->price_cny;
                    }else{
                        $settle_price = empty($data->contractGoods->settlement->price_cny)?$data->contractGoods->settlement->price:$data->contractGoods->settlement->price_cny;
                    }

                    $item->settle_price = new Price($settle_price, ConstantMap::CURRENCY_RMB);
                    $contractGoodsPrice = self::getContractGoodsPrice($data->contractGoods,$entity->price_type);
                    $item->contract_price = new Price($contractGoodsPrice, ConstantMap::CURRENCY_RMB);
                    $entity->addItem($item);
            }
        }
        return $entity;
    }

    /**
     * @name:getContractGoodsPrice
     * @desc: 获取合同商品价格
     * @param:* @param $contractGoods
     * @param:* @param $price_type 计价方式
     * @throw:
     * @return:mixed
     */
    protected static function getContractGoodsPrice($contractGoods,$price_type){
        if($price_type==ConstantMap::PRICE_TYPE_STATIC) {
            if ($contractGoods->currency == ConstantMap::CURRENCY_RMB)
                return $contractGoods->price;
            else {
                return ($contractGoods->price * $contractGoods->contract->exchange_rate);
            }
        }else{
            return ThirdPriceService::getGoodsPrice($contractGoods->contract_id,$contractGoods->goods_id);
        }

    }
    public function store(IAggregateRoot $entity){

    }

    /**
     * 查询合同下所有的入库通知单
     * @param $contractId
     * @return StockNotice
     */
    public function findByContractId($contractId) {
        $condition = "t.contract_id=" . $contractId;
        return $this->findAll($condition);
    }

}