<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 11:58:25.
 * Desc:合同终止
 */

namespace ddd\Split\Domain\Model\Contract;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\infrastructure\Utility;
use ddd\Split\Repository\Stock\StockInRepository;
use ddd\Split\Repository\Stock\StockOutRepository;

class ContractTerminate extends BaseEntity implements IAggregateRoot
{

    use ContractTerminateRepository;

    /**
     * 提交事件
     */
    const EVENT_AFTER_SUBMIT = "onAfterSubmit";

    /**
     * 驳回事件
     */
    const EVENT_AFTER_CHECK_BACK = "onAfterCheckBack";

    /**
     * 通过事件
     */
    const EVENT_AFTER_CHECK_PASS = "onAfterCheckPass";

    #region property

    /**
     * ID
     * @var   int
     */
    public $id = 0;

    /**
     * 合同ID
     * @var   int
     */
    public $contract_id;

    /**
     * 终止理由
     * @var   string
     */
    public $reason;

    /**
     * 附件
     * @var   array
     */
    public $files;

    /**
     * 状态
     * @var   int
     */
    public $status = 0;

    /**
     * 状态变更时间
     */
    public $status_time;

    #endregion

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events() {
        return [
            static::EVENT_AFTER_SUBMIT,
            static::EVENT_AFTER_CHECK_BACK,
            static::EVENT_AFTER_CHECK_PASS,
        ];
    }

    /**
     * 创建对象
     */
    public static function create(Contract $contract = null) {
        $entity = new static();
        if (!empty($contract)) {
            $entity->contract_id = $contract->contract_id;
        }
        $entity->status = ContractTerminateStatus::STATUS_NEW;
        $entity->status_time = Utility::getNow();
        return $entity;
    }

    /**
     * 是否可以编辑
     */
    public function isCanEdit() {
        return $this->status < ContractTerminateStatus::STATUS_SUBMIT;
    }

    /**
     * 是否可以终止
     */
    public function isCanTerminate(Contract $contract) {
        $flag = false;

        if (empty($contract)) {
            ExceptionService::throwArgumentNullException("Contract 对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $isCanTerminateBySplit = $this->isCanTerminateBySplit($contract);
        if ($isCanTerminateBySplit !== true) {
            return $isCanTerminateBySplit;
        }
        //入库单是否存在 待审核和待提交
        if ($contract instanceof BuyContract) {//采购合同
            $stockIns = StockInRepository::repository()->findAllByContractId($contract->contract_id);
            if (\Utility::isNotEmpty($stockIns)) {
                foreach ($stockIns as $stockIn) {
                    if ($stockIn->isOnChecking()) {
                        ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Terminate_Stock_In);
                        break;
                    }
                }
            }
        }
        //出库单是否存在 待审核和待提交
        if ($contract instanceof SellContract) {
            $stockOuts = StockOutRepository::repository()->findAllByContractId($contract->contract_id);
            if (\Utility::isNotEmpty($stockOuts)) {
                foreach ($stockOuts as $stockOut) {
                    if ($stockOut->isOnChecking()) {
                        ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Terminate_Stock_Out);
                        break;
                    }
                }
            }
        }
        //付款申请单处于“待审核”状态 原合同存在实付金额和认领金额>0时，不能进行合同终止操作
        $payApplication = \PayApplication::model()->find('t.contract_id=' . $contract->contract_id . ' AND t.status=' . \PayApplication::STATUS_SUBMIT);
        if (!empty($payApplication)) {
            return "当前合同（" . $contract->contract_code . "）付款申请单处于待审核状态,不能发起终止！";
//            return $flag;
        }

        if ($this->isCanTerminateByPay($contract) !== true) {
            return "当前合同（" . $contract->contract_code . "）存在实付金额和认领金额>0,不能发起终止！";
        }

        //进项票或销项票申请处于“待审核”状态,原合同进项票金额或销项票金额>0时，不能进行合同终止操作
        $invoiceApplication = \InvoiceApplication::model()->find('t.contract_id=' . $contract->contract_id . ' AND t.status=' . \InvoiceApplication::STATUS_CHECKING);
        if (!empty($invoiceApplication)) {
            return "当前合同（" . $contract->contract_code . "）进项票或销项票申请处于待审核状态,进项票金额或销项票金额>0,不能发起终止！";
        }
        return true;
    }

    /**
     * @desc 合同是否可以终止（收付款相关）
     */
    private function isCanTerminateByPay($contract) {
        $flag = false;
        $payAmount = \PayService::getContractActualPaidAmount($contract->contract_id);
        $receiveAmount = \ReceiveConfirmService::getReceivedAmountByContractId($contract->contract_id);
        if ($contract instanceof BuyContract) {//采购合同
            $amount = $payAmount - $receiveAmount;//已实付-已认领
        }
        if ($contract instanceof SellContract) {//销售合同
            $amount = $receiveAmount - $payAmount;//已认领-已实付
        }
        if ($amount != 0) {
            return $flag;
        }
        return true;
    }

    /**
     * @desc  合同是否可以终止（合同拆分相关条件）
     * @param $contract 合同实体
     * @return bool
     */
    private function isCanTerminateBySplit($contract) {
        $flag = false;
        //合同已被终止过
        $terminateInfo = $this->getContractTerminateRepository()->findByContractId($contract->contract_id);
        if (!empty($terminateInfo) && $terminateInfo->status == ContractTerminateStatus::STATUS_SUBMIT) {
            return '合同已经被终止过，不能进行终止操作';
        }

        //原合同未被平移
        if ($contract->isNotSplit()) {
            return '原合同未被平移，不能进行终止操作';
        }

        //存在待审核的合同平移
        if ($contract->hasContractSplitApplyOnChecking()) {
            return '原合同存在待审核的合同平移，不能进行终止操作';
        }

        //存在待审核的出入库平移
        if ($contract->hasStockSplitApplyOnChecking()) {
            return "当前合同（" . $contract->contract_code . "）存在待审核的出入库平移,不能进行终止操作！";
        }
        //合同可平移商品数量不为0
        if ($contract->hasGoods()) {
            return "当前合同（" . $contract->contract_code . "）可平移商品数量不为0,不能进行终止操作！";;
        }
//        return "销售合同（".$contract->contract_code."）下发货单（".$row->code."）未结算，不能发起合同结算！";
        //合同入库可平移数量不为0
        if ($contract instanceof BuyContract) {
            if (\Utility::isNotEmpty($contract->getCanSplitStockBills())) {
                return "当前合同（" . $contract->contract_code . "）入库可平移数量不为0,不能发起终止！";
            }
        }
        //合同出库可平移数量不为0
        if ($contract instanceof SellContract) {
            if (\Utility::isNotEmpty($contract->getCanSplitStockBills())) {
                return "当前合同（" . $contract->contract_code . "）出库可平移数量不为0,不能发起终止！";
            }
        }
        return true;
    }


    public function isCanSubmit() {
        return in_array($this->status, [ContractTerminateStatus::STATUS_BACK, ContractTerminateStatus::STATUS_NEW]);
    }


    /**
     * 合同终止提交
     * @param bool $persistent 是否持久化
     */
    public function submit($persistent = true) {
        $this->status = ContractTerminateStatus::STATUS_SUBMIT;
        $this->status_time = Utility::getNow();
        if ($persistent) {
            $this->getContractTerminateRepository()->submit($this);
        }
        $this->publishEvent(static::EVENT_AFTER_SUBMIT, new ContractTerminateSubmittedEvent($this));
    }

    /**
     * 添加附件
     */
    public function addFiles(Attachment $file) {
        if (empty($file)) {
            ExceptionService::throwArgumentNullException("Attachment对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }
        $this->files[$file->id] = $file;
    }

    /**
     * 删除附件
     */
    public function removeFiles(Attachment $file) {
        if (isset($this->files[$file->id])) {
            unset($this->files[$file->id]);
        }
    }

    /**
     * 审核通过
     */
    public function checkPass($persistent = true) {
        $this->status = ContractTerminateStatus::STATUS_PASS;
        $this->status_time = Utility::getNow();
        if ($persistent) {
            $this->getContractTerminateRepository()->checkPass($this);
        }
        $this->publishEvent(static::EVENT_AFTER_CHECK_PASS, new ContractTerminatedEvent($this));

    }

    /**
     * 审核驳回
     */
    public function checkBack($persistent = true) {
        $this->status = ContractTerminateStatus::STATUS_BACK;
        $this->status_time = Utility::getNow();
        if ($persistent) {
            $this->getContractTerminateRepository()->checkBack($this);
        }
        $this->publishEvent(static::EVENT_AFTER_CHECK_BACK, new ContractTerminateRejectEvent($this));
    }

    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->id = $value;
    }
}
