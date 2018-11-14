<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/28 11:09
 * Describe：
 */

namespace ddd\repository\contract;


use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\contract\ContractGoods;
use ddd\domain\entity\contract\ContractItem;
use ddd\domain\entity\value\Quantity;
use ddd\domain\enum\MainEnum;
use ddd\Common\IAggregateRoot;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\Utility;
use ddd\Common\Repository\EntityRepository;

class ContractRepository extends EntityRepository implements IContractRepository
{

    public function init()
    {
        $this->with=array("goods","extra");
    }

    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return \Contract::class;//"Contract";
    }

    /**
     * @return \ddd\Common\Domain\BaseEntity|Contract
     * @throws \Exception
     */
    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new Contract();
    }




    /**
     * @param $model
     * @return Contract
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity=Contract::create();
        $entity->setAttributes($model->getAttributes(),false);
        if($model->is_main==MainEnum::IS_MAIN)
            $entity->is_main=true;
        else
            $entity->is_main=false;


        if(is_array($model->goods))
        {
            foreach ($model->goods as $d)
            {
                $item=ContractGoods::create();
                $item->setAttributes($d->getAttributes());
                $item->quantity=new Quantity($d->quantity,$d->unit);
                $entity->addGoodsItem($item);
            }
        }
        if(!empty($model->extra)){
            $extra=json_decode($model->extra->content,true);
            foreach ($extra as $item){
                $contractExtra=new ContractItem();
                $contractExtra->key=$item['key'];
                $contractExtra->name=$item['name'];
                $contractExtra->content=$item['display_value'];
                $contractExtra->content_type=$item['value'];
                $entity->addItem($contractExtra);
            }
        }
        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        $id = $entity->getId();
        if (!empty($id))
        {
            $model = \Contract::model()->with("goods")->findByPk($id);
        }
        if (empty($model))
        {
            $model = new \Contract();
        }
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);
        if($entity->is_main)
            $model->is_main=MainEnum::IS_MAIN;
        else
            $model->is_main=MainEnum::IS_NOT_MAIN;

        $isNew = $model->isNewRecord;

        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);

        $items = $entity->goods_items;
        if (!is_array($items))
            $items = array();
        if (!$isNew)
        {
            if (is_array($model->goods))
            {
                foreach ($model->goods as $d)
                {
                    if (isset($items[$d->goods_id]))
                    {
                        $item = $items[$d->goods_id];
                        $itemValues = $item->getAttributes();
                        $itemValues = \Utility::unsetCommonIgnoreAttributes($itemValues);
                        $itemValues["quantity"]=$item->quantity->quantity;
                        $itemValues["unit"]=$item->quantity->unit;
                        unset($itemValues["detail_id"]);
                        $d->setAttributes($itemValues);
                        $res = $d->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($d);
                        unset($items[$d->goods_id]);
                    }
                    else
                    {
                        $res = $d->delete();
                        if (!$res)
                            throw new ZModelDeleteFalseException($d);
                    }
                }
            }
        }


        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $d = new \ContractGoods();
                $itemValues = $item->getAttributes();
                unset($itemValues["detail_id"]);
                $itemValues = \Utility::unsetCommonIgnoreAttributes($itemValues);
                $itemValues["quantity"]=$item->quantity->quantity;
                $itemValues["unit"]=$item->quantity->unit;
                $d->setAttributes($itemValues);

                $d->project_id = $model->project_id;
                $d->contract_id = $model->contract_id;

                $res = $d->save();
                if (!$res)
                    throw new ZModelSaveFalseException($d);

            }
        }
        $entity->setId($model->getPrimaryKey());
        return $entity;
    }

    protected function saveGoodsItems(Contract $contract)
    {

    }

    protected function savePaymentPlans(Contract $contract)
    {

    }

    /**
     * 更新合同状态
     * @param Contract $entity
     * @throws \Exception
     */
    protected function updateStatus(Contract $entity)
    {
        $model=\Contract::model()->findByPk($entity->contract_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->contract_id, "Contract");

        if($model->status != $entity->status)
        {
            $model->old_status = $model->status;
            $model->status = $entity->status;
            $model->status_time = Utility::getNow();
            $model->update_user_id = \Utility::getNowUserId();
            $model->update_time = Utility::getNow();
            $res= $model->update(["old_status","status","status_time","update_user_id","update_time"]);
            if(!$res)
                throw new ZModelSaveFalseException($model);
        }
    }

    /**
     * 保存提交
     * @param Contract $contract
     * @throws \Exception
     */
    public function submit(Contract $contract)
    {
        // TODO: Implement submit() method.
        $this->updateStatus($contract);
    }

    /**
     * 作废
     * @param Contract $contract
     * @throws \Exception
     */
    public function trash(Contract $contract)
    {
        // TODO: Implement submit() method.
        $this->updateStatus($contract);
    }

    /**
     * 驳回
     * @param Contract $contract
     * @throws \Exception
     */
    public function back(Contract $contract)
    {
        // TODO: Implement submit() method.
        $this->updateStatus($contract);
    }

    /**
     * 设置为结算驳回
     * @param Contract $contract
     * @throws \Exception
     */
    public function setSettledBack(Contract $contract)
    {
        $this->updateStatus($contract);
    }

    /**
     * 设置为结算中
     * @param Contract $contract
     * @throws \Exception
     */
    public function setOnSettling(Contract $contract)
    {
        $this->updateStatus($contract);
    }

    /**
     * 设置为结算完成
     * @param Contract $contract
     * @throws \Exception
     */
    public function setSettled(Contract $contract)
    {
        $this->updateStatus($contract);
    }


    /**
     * 设置为完结
     * @param Contract $contract
     * @throws \Exception
     */
    public function setDone(Contract $contract)
    {
        $this->updateStatus($contract);
    }

    /**
     * 合同文档已上传
     * @param Contract $contract
     * @throws \Exception
     */
    public function setFileUploaded(Contract $contract)
    {
        $this->updateStatus($contract);
    }

    /**
     * 合同电子双签文档已上传
     * @param Contract $contract
     * @throws \Exception
     */
    public function setSignedFileUploaded(Contract $contract)
    {
        $this->updateStatus($contract);
    }

    /**
     * 合同最终合同纸质文件已上传
     * @param Contract $contract
     * @throws \Exception
     */
    public function setPaperUploaded(Contract $contract)
    {
        $this->updateStatus($contract);
    }

    /**
     * 设置合同为已拆分
     * @param Contract $contract
     */
    public function setSplit(Contract $contract) {
        $model = \Contract::model()->findByPk($contract->contract_id);
        if (empty($model))
            throw new ZModelNotExistsException($contract->contract_id, "Contract");

        if ($model->split_type != $contract->split_type) {
            $model->split_type = $contract->split_type;
            $model->update_user_id = \Utility::getNowUserId();
            $model->update_time = Utility::getNow();
            $res = $model->update(["split_type", "update_user_id", "update_time"]);
            if (!$res)
                throw new ZModelSaveFalseException($model);
        }
    }

    /**
     * 设置合同终止中
     * @param Contract $contract
     * @throws \Exception
     */
    public function setTerminating(Contract $contract){
        $this->updateStatus($contract);
    }

    /**
     * 设置合同终止驳回
     * @param Contract $contract
     * @throws \Exception
     */
    function setTerminateBack(Contract $contract) {
        $this->updateStatus($contract);
    }

    /**
     * 设置合同已终止
     * @param Contract $contract
     * @throws \Exception
     */
    function setTerminated(Contract $contract) {
        $this->updateStatus($contract);
    }


}