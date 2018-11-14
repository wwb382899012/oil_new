<?php
/**
 * Created by PhpStorm.
 * User: liyu
 * Date: 2018/6/12
 * Time: 11:05
 * Desc:合同终止应用层Service
 */

namespace ddd\Split\Application;


use ddd\Common\Application\TransactionService;
use ddd\domain\entity\Attachment;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\infrastructure\Utility;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\Split\Domain\Model\Contract\ContractRepository;
use ddd\Split\Domain\Model\Contract\ContractTerminate;
use ddd\Split\Domain\Model\Contract\ContractTerminateRepository;
use ddd\Split\Domain\Model\Contract\ContractTerminateStatus;
use ddd\Split\Domain\Model\Contract\SellContract;
use ddd\Split\Dto\AttachmentDTO;
use ddd\Split\Dto\ContractTerminate\ContractTerminateDTO;

class ContractTerminateService extends TransactionService
{

    use ContractTerminateRepository;
    use ContractRepository;


    /**
     * 获取合同终止信息
     * @param $contractId
     * @return array
     */
    public function getContractTerminate($contractId) {
        $entity = $this->getContractTerminateRepository()->findByContractId($contractId);
        $checkLogs = [];
        $contractTerminate = [];
        if (!empty($entity)) {
            $checkLogs = \FlowService::getCheckLog($entity->contract_id, \FlowService::BUSINESS_CONTRACT_TERMINATE_CHECK);
        } else {
            $entity = ContractTerminate::create();
        }
        $contractTerminateDTO = new ContractTerminateDTO();
        $contractTerminateDTO->fromEntity($entity);
        $contractTerminate = $contractTerminateDTO->getAttributes();
        $contractTerminate['audit_log'] = $checkLogs;
        return $contractTerminate;
    }

    /**
     * 保存终止信息
     * @param ContractTerminateDTO $contractTerminateDTO
     * @return bool|string
     */
    public function save(ContractTerminateDTO $contractTerminateDTO, $contract) {
        if (empty($contract)) {
            ExceptionService::throwArgumentNullException("Contract 对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        if (!$contractTerminateDTO->validate()) {
            return $contractTerminateDTO->getErrors();
        }
        try {
            $entity = $contractTerminateDTO->toEntity();
            $contactEntity = $this->getContractRepository()->findByPk($contract->contract_id);
            $isCanTerminate = $entity->isCanTerminate($contactEntity);
//            $isCanTerminate=true; //TODO
            if ($isCanTerminate !== true) {//不能终止//TODO
                if (is_string($isCanTerminate)) {
                    throw new ZException($isCanTerminate);
                } else {
                    ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Terminate);
                }
            }

            if ($entity->isCanEdit() !== true) {//不能编辑
                ExceptionService::throwBusinessException(BusinessError::Contract_Terminate_Cannot_Edit);
            }

            $this->beginTransaction();

            $this->getContractTerminateRepository()->store($entity);

            $this->commitTransaction();
            return true;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            return $e->getMessage();
        }

    }


    /**
     * 提交
     * @param ContractTerminate $entity
     * @param bool $persistent 是否持久化
     * @return bool|void
     * @throws \Exception
     */
    public function submit(ContractTerminate $entity, $persistent = true) {
        if (!empty($entity)) {
            try {

                if ($this->isCanSubmit($entity) !== true) {
                    ExceptionService::throwBusinessException(BusinessError::Contract_Terminate_Cannot_Submit);
                }

                $this->beginTransaction();

                $entity->submit($persistent);

                $this->commitTransaction();
                return true;
            } catch (\Exception $e) {
                $this->rollbackTransaction();
                return $e->getMessage();
            }

        } else {
            return false;
        }
    }

    /**
     * 合同终止是否可以提交
     * @param ContractTerminate|null $entity
     * @return bool
     * @throws \Exception
     */
    public function isCanSubmit(ContractTerminate $entity = null) {
        try {
            if (!empty($entity)) {
                return $entity->isCanSubmit();
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 合同终止是否可以编辑
     * @param ContractTerminate|null $entity
     * @return bool
     * @throws \Exception
     */
    public function isCanEdit(ContractTerminate $entity = null) {
        try {
            if (!empty($entity)) {
                return $entity->isCanEdit();
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 合同终止是否可以终止
     * @param ContractTerminate|null $entity
     * @return bool
     * @throws \Exception
     */
    public function isCanTerminate(ContractTerminate $entity = null, $contract = null) {
        try {
            if (!empty($entity) && !empty($contract)) {
                $isCanTerminate = $entity->isCanTerminate($contract);
                if ($isCanTerminate !== true) {//不能终止//TODO
                    if (is_string($isCanTerminate)) {
                        throw new ZException($isCanTerminate);
                    } else {
                        ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Terminate);
                    }
                }
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            \Mod::log('isCanTerminate Feiled file:' . $e->getFile() . ';line:' . $e->getLine().';message:'.$e->getMessage());
            \Mod::log('isCanTerminate Feiled file:' . $e->getTraceAsString());
            return $e->getMessage();
        }
    }


    /**
     * 审核通过
     * @param ContractTerminate $entity
     * @param bool $persistent
     * @return bool|void
     * @throws \Exception
     */
    public function checkPass(ContractTerminate $entity, $persistent = true) {
        if (!empty($entity)) {
            try {
                $this->beginTransaction();

                $entity->checkPass($persistent);

                $this->commitTransaction();
                return true;
            } catch (\Exception $e) {
                $this->rollbackTransaction();
                return $e->getMessage();
            }
        } else {
            return false;
        }
    }

    /**
     * 审核驳回
     * @param ContractTerminate $entity
     * @param bool $persistent
     * @return bool|void
     * @throws \Exception
     */
    public function checkBack(ContractTerminate $entity, $persistent = true) {
        if (!empty($entity)) {
            try {
                $this->beginTransaction();

                $entity->checkBack($persistent);
                $this->commitTransaction();

                return true;
            } catch (\Exception $e) {
                $this->rollbackTransaction();
                return $e->getMessage();
            }
        } else {
            return false;
        }
    }

    /**
     * 判断合同终止是否可以修改，临时性业务逻辑，列表用
     * @param $status
     * @return bool
     */
    public static function terminateIsCanEdit($status) {
        return $status == ContractTerminateStatus::STATUS_BACK || $status == ContractTerminateStatus::STATUS_NEW;
    }

    public function assignDTO($params) {
        $dto = new ContractTerminateDTO();
        $files = $params['files'];
        unset($params['files']);
        $dto->setAttributes($params);
        if (\Utility::isNotEmpty($files)) {
            foreach ($files as $file) {
                $attachment = new AttachmentDTO();
                $attachment->setAttributes($file);
                $dto->files[$file['id']] = $attachment;
            }
        }
        return $dto;
    }

}