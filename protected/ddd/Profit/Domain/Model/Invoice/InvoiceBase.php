<?php
/**
 * User: liyu
 * Date: 2018/8/10
 * Time: 14:17
 * Desc: InvoiceBase.php
 */

namespace ddd\Profit\Domain\Model\Invoice;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\Contract;
use ddd\infrastructure\DIService;

class InvoiceBase extends BaseEntity
{
    #region property

    /**
     * 合同ID
     * @var   int
     */
    public $contract_id;

    /**
     * @var 合同类型
     */
    public $type;

    /**
     * @var 发票申请
     */
    public $invoice_application;

    /**
     * @var 开票信息
     */
    public $invoice;

    /**
     * 已收票金额
     * @var   float
     */
    private $buy_invoice_amount;

    /**
     * 已开票金额
     * @var   float
     */
    private $sell_invoice_amount;

    /**
     * 合同 进项票审核通过
     */
    const EVENT_INPUT_INVOICE_CHECK_PASS = "onInputInvoiceCheckPass";

    /**
     * 合同 销项票开票审核通过
     */
    const EVENT_INVOICE_CHECK_PASS = "onInvoiceCheckPass";

    #endregion


    /**
     * @param Contract|null $contract 合同对象
     * @param int $invoice_apply_id 发票申请ID（开票时请给0）
     * @param int $invoice_id 销项票开票ID
     * @return static
     * @throws \Exception
     */
    public static function create(Contract $contract = null, $invoice_apply_id = 0, $invoice_id = 0) {
        if (empty($contract)) {
            ExceptionService::throwArgumentNullException("Contract对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $entity = new static();
        $entity->contract_id = $contract->contract_id;
        $entity->type = $contract->type;
        if ($invoice_apply_id) {
            $entity->invoice_application = DIService::getRepository(IInvoiceApplicationRepository::class)->findByPk($invoice_apply_id);
        }
        if ($invoice_id) {
            $entity->invoice = DIService::getRepository(IInvoiceRepository::class)->findByPk($invoice_id);
        }
        return $entity;
    }

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events() {
        return [
            static::EVENT_INPUT_INVOICE_CHECK_PASS,
            static::EVENT_INVOICE_CHECK_PASS,
        ];
    }

    /**
     * 获取已收票金额
     */
    public function getBuyInvoiceAmount() {
        if (!$this->buy_invoice_amount) {
            $amount = 0;
//            $sql = 'select ifnull(sum(b.amount), 0) total_amount from t_invoice_application a
//                    left join t_invoice_application_detail b on b.apply_id = a.apply_id
//                    where a.invoice_type=' . \InvoiceApplication::TYPE_BUY .
//                ' and a.contract_id=' . $this->contract_id . ' and a.status>=' . \InvoiceApplication::STATUS_PASS .
//                ' and a.type_sub=' . \InvoiceApplication::SUB_TYPE_GOODS;
//            $data = \Utility::query($sql);
//            if (\Utility::isNotEmpty($data)) {
//                $amount = $data[0]['total_amount'];
//            }
            if (!empty($this->invoice_application)) {
                if ($this->invoice_application->isCheckPass()
                    && $this->invoice_application->isSubTypeGoods()
                    && $this->invoice_application->isTypeBuy()
                ) {
                    if (\Utility::isNotEmpty($this->invoice_application->application_details)) {
                        foreach ($this->invoice_application->application_details as $item) {
                            $amount += $item->amount;
                        }
                    }
                }
            }
            $this->buy_invoice_amount = $amount;
        }
        return $this->buy_invoice_amount;
    }

    /**
     * 获取已开票金额
     */
    public function getSellInvoiceAmount() {
        if (!$this->sell_invoice_amount) {
            $amount = 0;
//            $sql = 'select ifnull(sum(b.amount), 0) total_amount from t_invoice a
//                    left join t_invoice_application c on c.apply_id = a.apply_id
//                    left join t_invoice_detail b on b.invoice_id = a.invoice_id
//                    where a.contract_id=' . $this->contract_id . ' and a.status>=' . \Invoice::STATUS_PASS .
//                ' and c.type_sub=' . \InvoiceApplication::SUB_TYPE_GOODS;
//            $data = \Utility::query($sql);
//            if (\Utility::isNotEmpty($data)) {
//                $amount = $data[0]['total_amount'];
//            }
            if (!empty($this->invoice)&& !empty($this->invoice->invoice_application)) {
                if ($this->invoice->isCheckPass() && $this->invoice->invoice_application->isSubTypeGoods()) {
                    if (\Utility::isNotEmpty($this->invoice->invoice_application->application_details)) {
                        foreach ($this->invoice->invoice_application->application_details as $item) {
                            $amount += $item->amount;
                        }
                    }
                }
            }
            $this->sell_invoice_amount = $amount;
        }
        return $this->sell_invoice_amount;
    }

    /**
     * @desc 进项票审核通过
     */
    public function InputInvoiceCheckPass() {
        $this->publishEvent(static::EVENT_INPUT_INVOICE_CHECK_PASS, new InputInvoiceCheckPassEvent($this));
    }

    /**
     * @desc 销项票开票审核通过
     */
    public function InvoiceCheckPass() {
        $this->publishEvent(static::EVENT_INVOICE_CHECK_PASS, new InvoiceCheckPassEvent($this));
    }
}