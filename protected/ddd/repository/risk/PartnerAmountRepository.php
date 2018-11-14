<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:15
 * Describe：
 */

namespace ddd\repository\risk;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;

abstract class PartnerAmountRepository extends EntityRepository
{

    abstract function getType();

    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "PartnerAmount";
    }

    /**
     * 数据模型转换成业务对象
     *      一般子类需要重写该方法
     * @param $model
     * @return BaseEntity
     */
    public function dataToEntity($model)
    {
        $entity=$this->getNewEntity();
        if(!empty($entity))
        {
            $entity->setAttributes($model->getAttributes(),false);
        }
        $entity->amount = $model->used_amount;
        return $entity;
    }

    public function findByPartnerId($partnerId)
    {
        $model= $this->model()->with($this->with)->find("partner_id=".$partnerId." and type=".$this->getType());
        if(empty($model))
            return null;

        return $this->dataToEntity($model);
    }

    /**
     * @param IAggregateRoot $entity
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        $model= $this->model()->with($this->with)->find("partner_id=".$entity->partner_id." and type=".$this->getType());
        if (empty($model)) {
            $this->activeRecordClassName=$this->getActiveRecordClassName();
            $model=new $this->activeRecordClassName;
        }

        //这里需要处理一下新增时设置主键值的问题
        $params = $entity->getAttributes();
        $params['type'] = $this->getType();
        unset($params['id']);
        $model->setAttributes($params,false);
        /*if($model->isNewRecord)
            $model->setPrimaryKey(null);*/
        $model->save();
    }

    /**
     * 增加金额
     * @param $partnerAmount
     * @param $amount
     * @throws \Exception
     */
    public function addAmount($partnerAmount,$amount)
    {
        $res=\PartnerAmount::model()->updateByPk($partnerAmount->getId(),array("used_amount"=>new \CDbExpression("used_amount+".$amount)));
        if($res!==1)
            throw new \Exception("更新失败");
    }

    /**
     * 减少金额
     * @param $partnerAmount
     * @param $amount
     * @throws \Exception
     */
    public function subtractAmount($partnerAmount,$amount)
    {
        $res=\PartnerAmount::model()->updateByPk($partnerAmount->getId(),array("used_amount"=>new \CDbExpression("used_amount-".$amount)));
        if($res!==1)
            throw new \Exception("更新失败");
    }

    /**
     * 增加冻结金额
     * @param $partnerAmount
     * @param $amount
     * @throws \Exception
     */
    public function freezeAmount($partnerAmount,$amount)
    {
        $res=\PartnerAmount::model()->updateByPk($partnerAmount->getId(),array("frozen_amount"=>new \CDbExpression("frozen_amount+".$amount)));
        if($res!==1)
            throw new \Exception("更新失败");
    }

    /**
     * 解除冻结金额
     * @param $partnerAmount
     * @param $amount
     * @throws \Exception
     */
    public function unfreezeAmount($partnerAmount,$amount)
    {
        $res=\PartnerAmount::model()->updateByPk($partnerAmount->getId(),array("frozen_amount"=>new \CDbExpression("frozen_amount-".$amount)));
        if($res!==1)
            throw new \Exception("更新失败");
    }
}