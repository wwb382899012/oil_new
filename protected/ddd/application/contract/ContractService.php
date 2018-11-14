<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：
 */

namespace ddd\application\contract;


use ddd\application\dto\contract\ContractDTO;
use ddd\application\dto\stock\LadingBillDTO;
use ddd\Common\Application\TransactionService;
use ddd\domain\entity\contract\Contract;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\domain\service\risk\event\ContractRejectEvent;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZException;
use ddd\repository\contract\ContractRepository;
use ddd\repository\stock\LadingBillRepository;

class ContractService extends TransactionService
{

    use \ddd\domain\tRepository\contract\ContractRepository;

    //protected $contractRepository;
    protected $ladingRepository;

    public function __construct()
    {
        //$this->contractRepository = DIService::getRepository(IContractRepository::class);
        $this->ladingRepository = new LadingBillRepository();
    }

    /**
     * 创建合同的提单
     * @param Contract $contract
     * @return bool|\ddd\domain\entity\stock\LadingBill
     * @throws \Exception
     */
    public function createLadingBill(Contract $contract)
    {
        return $contract->createLadingBill();
    }

    /**
     * @desc 保存合同
     * @param ContractDTO $contract
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function saveContract(ContractDTO $contract)
    {
        if (!$contract->validate())
        {
            return $contract->getErrors();
        }

        $entity = $contract->toEntity();
        if (!$entity->validate())
        {
            return $entity->getErrors();
        }

        try
        {
            $this->beginTransaction();

            $this->getContractRepository()->store($entity);

            $this->commitTransaction();

            return true;
        } catch (\Exception $e)
        {
            \Mod::log((string)$e, "error");
            $this->rollbackTransaction();
            if ($this->isInOutTrans)
            {
                throw $e;
            } else
            {
                return false;
            }
        }
    }

    /**
     * @desc 提交合同
     * @param $contractId
     * @param Contract $contractEntity
     * @return string|bool|mixed
     * @throws \Exception
     */
    public function submitContract($contractId, $contractEntity)
    {
        try
        {
            if (empty($contractEntity))
            {
                $contractEntity = $this->getContractRepository()->findByPk($contractId);
                if (empty($contractEntity))
                {
                    throw new ZEntityNotExistsException($contractId, Contract::class);
                }
            }

            $this->beginTransaction();

            $contractService = new \ddd\domain\service\contract\ContractService();
            $contractService->contractSubmit($contractEntity);
            \FlowService::startFlowForCheck2($contractEntity->contract_id);
            \TaskService::doneTask($contractEntity->project_id, \Action::ACTION_10);
            //合同平移发起的商务确认
            \TaskService::doneTask($contractEntity->contract_id, \Action::ACTION_CONTRACT_SPLIT_BUSINESS_CONFIRM);

            $this->commitTransaction();

            return true;
        } catch (\Exception $e)
        {
            $this->rollbackTransaction();
            if ($this->isInOutTrans)
            {
                throw $e;
            } else
            {
                return $e->getMessage();
            }
        }
    }

    /**
     * 风控审核驳回
     * @param $contractId
     * @param Contract|null $contract
     * @return bool
     * @throws \Exception
     */
    public function riskRejectContract($contractId, Contract $contract = null)
    {
        try
        {
            if (empty($contract))
            {
                $contract = $this->getContractRepository()->findByPk($contractId);

                if (empty($contract->contract_id))
                {
                    throw new ZEntityNotExistsException($contractId, Contract::class);
                }
            }


            $this->beginTransaction();

            $contractService = new \ddd\domain\service\contract\ContractService();
            $contractService->riskCheckBack($contract);

            $this->commitTransaction();

            return true;
        } catch (\Exception $e)
        {
            $this->rollbackTransaction();
            if ($this->isInOutTrans)
            {
                throw $e;
            } else
            {
                return false;
            }
        }


    }

    /**
     * @desc 业务审核驳回
     * @param $contractId
     * @param Contract $contractEntity
     * @return bool
     * @throws \Exception
     */
    public function businessRejectContract($contractId, $contractEntity)
    {
        try
        {
            if (empty($contractEntity))
            {
                $contractEntity = $this->getContractRepository()->findByPk($contractId);

                if (empty($contractEntity->contract_id))
                {
                    throw new ZEntityNotExistsException($contractId, Contract::class);
                }
            }


            $this->beginTransaction();

            $contractService = new \ddd\domain\service\contract\ContractService();
            $contractService->businessCheckBack($contractEntity);

            $this->commitTransaction();

            return true;
        } catch (\Exception $e)
        {
            $this->rollbackTransaction();
            if ($this->isInOutTrans)
            {
                throw $e;
            } else
            {
                return $e->getMessage();
            }
        }
    }
}