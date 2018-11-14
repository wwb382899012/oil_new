<?php
/**
 * User: liyu
 * Date: 2018/8/14
 * Time: 16:12
 * Desc: InvoiceRepositoryRepository.php
 */

namespace ddd\Profit\Repository\Invoice;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Repository\EntityRepository;
use ddd\Profit\Domain\Model\Invoice\IInvoiceRepository;
use ddd\Profit\Domain\Model\Invoice\Invoice;
use ddd\Profit\Domain\Model\Invoice\InvoiceApplication;
use ddd\Profit\Domain\Model\Invoice\InvoiceApplicationDetail;

class InvoiceRepository extends EntityRepository implements IInvoiceRepository
{
    public $with = ['application', 'application.applyDetail'];

    /**
     * 获取新的实体对象
     * @return BaseEntity
     */
    public function getNewEntity() {
        return Invoice::create();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return 'Invoice';
    }

    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes());
        if (!empty($model->application)) {
            $invoiceApplication = InvoiceApplication::create();
            $invoiceApplication->setAttributes($model->application->getAttributes());
            if (\Utility::isNotEmpty($model->application->applyDetail)) {
                foreach ($model->application->applyDetail as $detail) {
                    $invoiceApplicationDetail = new InvoiceApplicationDetail();
                    $invoiceApplicationDetail->setAttributes($detail->getAttributes());
                    $invoiceApplication->addApplicationDetail($invoiceApplicationDetail);
                }
            }
            $entity->invoice_application = $invoiceApplication;
        }
        return $entity;
    }
}