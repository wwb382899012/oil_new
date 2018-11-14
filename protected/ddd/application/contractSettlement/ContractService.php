<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:29
 * Describe：
 */

namespace ddd\application\contractSettlement;

use ddd\application\dto\contractSettlement\ContractDTO;
use ddd\Common\Application\TransactionService;
use ddd\domain\entity\stock\StockIn;
use ddd\domain\iRepository\stock\IStockInRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\contract\ContractRepository;

class ContractService extends TransactionService
{
    /**
     * 获取合同信息
     * @param $contract_id 合同id
     * @return array|bool
     * @throws \Exception
     */
    public function getContract($contract_id)
    {
        $contractEntity = ContractRepository::repository()->findByPk($contract_id);
        if(!empty($contractEntity)){
            $ContractDTO = new ContractDTO();
            $ContractDTO->fromEntity($contractEntity);
            return $ContractDTO->getAttributes();
        }
        else
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contract_id));

    }
    /**
     * 合同是否可以结算
     * @param $contract_id 合同id
     * @return array|bool
     * @throws \Exception
     */
    public function isCanSettle($contract_id)
    {
        $contractEntity = ContractRepository::repository()->findByPk($contract_id);
        if(!empty($contractEntity)){
            $service = new \ddd\domain\service\contract\ContractService();
            $isCanSettle =  $service->isCanSettle($contractEntity);
            if(is_bool(isCanSettle)&&$isCanSettle)
                return true;
            else
                return $isCanSettle;
        }
        else {
            return \BusinessError::outputError(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contract_id));

        }

    }
}