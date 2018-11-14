<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/17 15:02
 * Describe：
 */

namespace ddd\domain\service\contract;


use ddd\domain\event\contract\FileUploadedEvent;
use ddd\domain\event\contractSettlement\ContractSettlementEvent;
use ddd\domain\event\contractSettlement\ContractSettlementRejectEvent;
use ddd\domain\event\contractSettlement\ContractSettlementSubmitEvent;
use ddd\domain\tRepository\contract\ContractRepository;
use ddd\Common\Domain\BaseService;
use ddd\infrastructure\error\ZException;
use ddd\Split\Domain\Model\Contract\ContractTerminatedEvent;
use ddd\Split\Domain\Model\Contract\ContractTerminateRejectEvent;
use ddd\Split\Domain\Model\Contract\ContractTerminateSubmittedEvent;

class ContractEventHandlerService extends BaseService
{
    use ContractRepository;


    /**
     * 当合同文本上传后触发的事件的响应动作
     * @param FileUploadedEvent $event
     * @throws \Exception
     */
    public function onAfterFileUploaded(FileUploadedEvent $event) {
        if (empty($event))
            throw new ZException("FileUploadedEvent is empty");
        $contract = $this->getContractRepository()->findByPk($event->sender->contract_id);
        $contract->setFileUploaded();
    }

    /**
     * 响应合同结算单审核通过事件
     * @param ContractSettlementEvent $event
     * @throws \Exception
     */
    public function onAfterContractSettled(ContractSettlementEvent $event) {
        if (empty($event))
            throw new ZException("ContractSettlementEvent is empty");
        $contract = $this->getContractRepository()->findByPk($event->sender->contract_id);
        $contract->setSettledAndSave();
    }


    /**
     * 响应合同结算单提交事件，标记合同为结算中
     * @param ContractSettlementSubmitEvent $event
     * @throws \Exception
     */
    public function onAfterContractSettlementSubmit(ContractSettlementSubmitEvent $event) {
        if (empty($event))
            throw new ZException("ContractSettlementSubmitEvent is empty");
        $contract = $this->getContractRepository()->findByPk($event->sender->contract_id);
        $contract->setOnSettlingAndSave();
    }

    /**
     * 响应合同结算单驳回事件，标记合同为驳回
     * @param ContractSettlementSubmitEvent $event
     * @throws \Exception
     */
    public function onAfterContractSettlementReject(ContractSettlementRejectEvent $event) {
        if (empty($event))
            throw new ZException("ContractSettlementRejectEvent is empty");
        $contract = $this->getContractRepository()->findByPk($event->sender->contract_id);
        $contract->setSettledBack();
    }


    /**
     * 响应合同终止中事件，标记合同终止中
     * @param ContractTerminatedEvent $event
     * @throws ZException
     */
    public function onAfterContractTerminateSubmitted(ContractTerminateSubmittedEvent $event) {
        if (empty($event)) {
            throw new ZException('ContractTerminateSubmittedEvent is Empty');
        }
        $contract = $this->getContractRepository()->findByPk($event->sender->contract_id);
        $contract->setTerminating();
    }

    /**
     * 响应合同终止被驳回事件
     * @param ContractTerminatedEvent $event
     * @throws ZException
     */
    public function onAfterContractTerminateBack(ContractTerminateRejectEvent $event) {
        if (empty($event)) {
            throw new ZException('ContractTerminateRejectEvent is Empty');
        }
        $contract = $this->getContractRepository()->findByPk($event->sender->contract_id);
        $contract->setTerminateBack();
    }

    /**
     * 响应合同已经终止事件
     * @param ContractTerminatedEvent $event
     * @throws ZException
     */
    public function onAfterContractTerminated(ContractTerminatedEvent $event) {
        if (empty($event)) {
            throw new ZException('ContractTerminatedEvent is Empty');
        }
        $contract = $this->getContractRepository()->findByPk($event->sender->contract_id);
        $contract->setTerminated();
    }
}