<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 14:27:59.
 * Desc:ContractTerminateRepository
 */

namespace ddd\Split\Repository\Contract;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\Attachment;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\Split\Domain\Model\Contract\ContractTerminate;
use ddd\Split\Domain\Model\Contract\IContractTerminateRepository;

class ContractTerminateRepository extends EntityRepository implements IContractTerminateRepository
{

    public function init() {
        $this->with = ["terminateAttachments", "contract"];
    }

    /**
     * 数据模型对象转为业务对象
     * @param $model
     * @return ContractTerminate
     */
    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        if (is_array($model->terminateAttachments) && !empty($model->terminateAttachments)) {
            foreach ($model->terminateAttachments as $attachment) {
                $file = Attachment::create();
                $file->id = $attachment->id;
                $file->file_url = $attachment->file_url;
                $file->name = $attachment->name;
                $entity->addFiles($file);
            }
        }
        return $entity;
    }

    /**
     * 把对象持久化到数据库
     * @param IAggregateRoot $entity
     * @return IAggregateRoot
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity) {
        if (empty($entity)) {
            throw new ZException('ContractTerminate对象不存在');
        }
        $contractId = $entity->contract_id;
        if ($contractId) {
            $model = \ContractTerminate::model()->find('t.contract_id='.$contractId);
        }
        $isNew = false;
        if (empty($model)) {
            $model = new \ContractTerminate();
            $model->contract_id = $entity->contract_id;
        }
        $model->reason = $entity->reason;
        $model->status = $entity->status;
        $model->status_time = $entity->status_time;
        $res = $model->save();
        if (!$res) {
            throw new ZModelSaveFalseException($model);
        }
        $entity->setId($model->getPrimaryKey());
        //更新附件的base_id
        if (\Utility::isNotEmpty($entity->files)) {
            foreach ($entity->files as $file) {
                \ContractTerminateAttachment::model()->updateByPk($file->id,['base_id' => $entity->getId()]);
            }
        }
        return $entity;
    }


    //put your code here
    public function checkBack(ContractTerminate $contractTerminate) {
        $this->updateStatus($contractTerminate);
    }

    public function checkPass(ContractTerminate $contractTerminate) {
        $this->updateStatus($contractTerminate);
    }

    public function getActiveRecordClassName(): string {
        return 'ContractTerminate';
    }

    public function getNewEntity(): BaseEntity {
        return ContractTerminate::create();
    }

    public function submit(ContractTerminate $contractTerminate) {
        $this->updateStatus($contractTerminate);
    }

    protected function updateStatus(ContractTerminate $contractTerminate) {
        if (empty($contractTerminate)) {
            throw new ZException("ContractTerminate对象不存在");
        }
        $model = \ContractTerminate::model()->findByPk($contractTerminate->getId());
        if (empty($model)) {
            throw new ZEntityNotExistsException($contractTerminate->id, 'contractTerminate');
        }
        if ($contractTerminate->status != $model->status) {
            $model->status = $contractTerminate->status;
            $res = $model->save();
            if (!$res) {
                throw new ZModelSaveFalseException($model);
            }
        }
        return true;
    }

    /**
     * 根据合同ID 获取合同终止信息
     * @param $contractId
     * @return BaseEntity|null
     */
    public function findByContractId($contractId) {
        $condition = 't.contract_id=' . $contractId;
        $entity = $this->find($condition);
        return $entity;
    }


}
