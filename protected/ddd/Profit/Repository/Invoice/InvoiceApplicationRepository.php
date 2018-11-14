<?php
/**
 * User: liyu
 * Date: 2018/8/14
 * Time: 14:53
 * Desc: InvoiceApplicationRepositorynRepository.php
 */

namespace ddd\Profit\Repository\Invoice;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Repository\EntityRepository;
use ddd\Profit\Domain\Model\Invoice\IInvoiceApplicationRepository;
use ddd\Profit\Domain\Model\Invoice\Invoice;
use ddd\Profit\Domain\Model\Invoice\InvoiceApplication;
use ddd\Profit\Domain\Model\Invoice\InvoiceApplicationDetail;

class InvoiceApplicationRepository extends EntityRepository implements IInvoiceApplicationRepository
{
    public $with = ['applyDetail'];

    /**
     * 获取新的实体对象
     * @return BaseEntity
     */
    public function getNewEntity() {
        return InvoiceApplication::create();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return 'InvoiceApplication';
    }

    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes());
        if (\Utility::isNotEmpty($model->applyDetail)) {
            foreach ($model->applyDetail as $detail) {
                $invoiceApplicationDetail = new InvoiceApplicationDetail();
                $invoiceApplicationDetail->setAttributes($detail->getAttributes());
                $entity->addApplicationDetail($invoiceApplicationDetail);
            }
        }
        return $entity;
    }
}