<?php
/**
 * User: liyu
 * Date: 2018/8/23
 * Time: 18:36
 * Desc: ProjectPaymentRepository.php
 */

namespace ddd\Profit\Repository\Payment;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Price;
use ddd\Profit\Application\ProfitService;
use ddd\Profit\Domain\Model\Payment\IProjectPaymentRepository;
use ddd\Profit\Domain\Model\Payment\ProjectPayment;

class ProjectPaymentRepository extends EntityRepository implements IProjectPaymentRepository
{

    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        $entity->pay_amount = new Price($model->pay_amount, \ConstantMap::CURRENCY_RMB);
        $entity->miscellaneous_fee = new Price($model->miscellaneous_fee, \ConstantMap::CURRENCY_RMB);
        return $entity;
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity
     */
    public function getNewEntity() {
        return ProjectPayment::create();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return 'ProjectPayment';
    }

    public function store(IAggregateRoot $entity) {
        //项目下付款 持久化
        $model = $this->model()->find('t.project_id=' . $entity->project_id);
        if (empty($model)) {
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);
        $model->pay_amount = $values['pay_amount']['price'];
        $model->miscellaneous_fee = $values['miscellaneous_fee']['price'];
        if (!$model->save()) {
            throw new ZModelSaveFalseException($model);
        }
        //项目下付款实付 事件TODO
        ProfitService::service()->addProjectProfitByProjectId($model->project_id);
        return $model;
    }


    public function findByProjectId($projectId) {
        return $this->find('t.project_id=' . $projectId);
    }

}