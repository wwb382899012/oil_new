<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：采购合同结算
 */

namespace ddd\application\contractSettlement;

use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\contractSettlement\BuyContractSettlement;
use ddd\domain\entity\contractSettlement\OtherExpenseSettlementItem;
use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\presentation\assemble\settlement\BuyContractSettlementAssemble;
//use ddd\domain\service\contract\ContractService;
use ddd\application\dto\contractSettlement\BuyContractSettlementDTO;
use ddd\application\dto\contractSettlement\ContractDTO;
use ddd\Common\Application\TransactionService;
use ddd\repository\contract\ContractRepository;
use ddd\repository\contractSettlement\BuyContractSettlementRepository;

class BuyContractSettlementService extends TransactionService
{

    protected $ContractRepository;
    protected $BuyContractSettlementRepository;

    public function __construct()
    {
        $this->ContractRepository = new ContractRepository();
        $this->BuyContractSettlementRepository = new BuyContractSettlementRepository();
    }
    /**
     * @desc 获取采购合同结算 信息
     * @param $contract_id 合同id
     * @return 结算单对象
     * @throws \Exception
     */
    public function getBuyContractSettlement($contract_id){
        $contractEntity = ContractRepository::repository()->findByPk($contract_id);
        $buyContractSettlementEntity = BuyContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        if($buyContractSettlementEntity->status==\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW)
            $buyContractSettlementEntity = $this->createStockContractSettlement($contractEntity);
        //有合同结算
        if(!is_string($buyContractSettlementEntity)) {
            $BuyContractSettlementDTO = new BuyContractSettlementDTO();
            $BuyContractSettlementDTO->fromEntity($buyContractSettlementEntity);
            //$a = new BuyContractSettlementAssemble();
            //print_r($a->assemble($BuyContractSettlementDTO)); //展示层接口
            return $BuyContractSettlementDTO->getAttributes();
        }
        else
            return $buyContractSettlementEntity;//返回异常
                
    }
    /**
     * @desc 对DTO进行赋值
     * @param $post=array(
     *  'settle_date'=>'',
     *  'settle_status'=>'',
     *  'goods_arr'=>[],
     *  'not_goods_arr'=>[]
     * )
     * @return DTO
     * @throws \Exception
     */
    public function AssignDTO($contract_id,$post){
        $contractEntity = ContractRepository::repository()->findByPk($contract_id);
        $stockContractSettlementEntity = \ddd\repository\contractSettlement\BuyContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        $isFirstSettle=true;//是第一次结算
        if($stockContractSettlementEntity->status==\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW)
            $stockContractSettlementEntity = $this->createStockContractSettlement($contractEntity);
        else{
            $isFirstSettle=false;//不是第一次结算
            if($stockContractSettlementEntity->status >= \ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_SUBMIT)
                return \BusinessError::outputError(\OilError::$BUY_CONTRACT_SETTLE_NOT_ALLOW_EDIT);
        }

        if(!is_string($stockContractSettlementEntity)) {
            $BuyContractSettlementDTO = new \ddd\application\dto\contractSettlement\BuyContractSettlementDTO();
            $BuyContractSettlementDTO->fromEntity($stockContractSettlementEntity);
            //赋值
            $SettleService = new \ddd\application\contractSettlement\SettleService();
            $BuyContractSettlementDTO = $SettleService->createNewDTO($BuyContractSettlementDTO,$post,$isFirstSettle);

            return $BuyContractSettlementDTO;
        }else
            return $stockContractSettlementEntity;
    }

    /*
     * 生成非货款上传附件时  detail_id
     * */
    public function createDetailId(){
        $item = new OtherExpenseSettlementItem();
        return $item->create()->detail_id;
    }
     /**
     * @desc 创建结算单对象
     * @param Contract $Contract
     * @return 结算单对象
     * @throws \Exception
     */
    public function createStockContractSettlement(Contract $Contract){
        
        try
        {
            return BuyContractSettlement::create($Contract);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        } 
    }
    /**
     * 保存结算单（采购合同结算）
     * @param BuyContractSettlementDTO $BuyContractSettlementDTO
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function saveStockContractSettlement(BuyContractSettlementDTO $BuyContractSettlementDTO)
    { 
        //数据格式验证，规则：保存数据时验证，暂存数据时不验证。
        if($BuyContractSettlementDTO->settle_status==2){
            if(!$BuyContractSettlementDTO->validate()){
                return $BuyContractSettlementDTO->getErrors();//验证不通过
            } else{
                if(!empty($BuyContractSettlementDTO->settlementGoods)){//商品结算
                    foreach ($BuyContractSettlementDTO->settlementGoods as $key=>$value){
                        if($value->hasDetail==1){//明细录入时，才验证
                            if(!$value->validate()){
                                return $value->getErrors();//验证不通过
                            }
                            else{
                                if(!$value->settlementGoodsDetail->validate()){
                                    return $value->settlementGoodsDetail->getErrors();//验证不通过
                                }else{
                                    //税收
                                    if(!empty($value->settlementGoodsDetail->tax_detail_item)){
                                        foreach ($value->settlementGoodsDetail->tax_detail_item as $e=>$f){
                                            if(!$f->validate())
                                                return $f->getErrors();//验证不通过
                                        }
                                    }
                                    //其他费用
                                    if(!empty($value->settlementGoodsDetail->other_detail_item)){
                                        foreach ($value->settlementGoodsDetail->other_detail_item as $m=>$n){
                                            if(!$n->validate())
                                                return $n->getErrors();//验证不通过
                                        }
                                    }//
                                }
                            }
                      }
                    }
                }
                
                if(!empty($BuyContractSettlementDTO->other_expense)){
                    foreach ($BuyContractSettlementDTO->other_expense as $p=>$q){
                        if(!$q->validate()){
                            return $q->getErrors();//验证不通过
                        }
                    }
                }//非货款金额
                
            }
        }
        
        try
        {
            $entity=$BuyContractSettlementDTO->toEntity();
            $this->beginTransaction();
            //print_r($entity);die;
            $this->BuyContractSettlementRepository->store($entity);
            $this->startFlow($entity);//发起审核流程
            $this->commitTransaction();
            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }
    /**
     * 发起 采购合同结算审核流程
     * @param $BuyContractSettlementEntity 
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function startFlow($BuyContractSettlementEntity){
        try {
            if ($BuyContractSettlementEntity->status == SettlementStatus::STATUS_SUBMIT) {
                \FlowService::startFlowForCheck21($BuyContractSettlementEntity->contract_id);
            }
           
        } catch (\Exception $e) {
          
        }
    }
    /**
     * @desc 是否可结算
     * @param ContractDTO $ContractDTO
     * @return boolean
     * @throws \Exception
     */
    public function isCanSettle(ContractDTO $ContractDTO)
    {
        $entity = $ContractDTO->toEntity();
        try
        {
            $service = new \ddd\domain\service\contract\ContractService();
            return $service->isCanSettle($entity);
            
        } catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @desc 是否可修改
     * @param ContractDTO $ContractDTO
     * @return boolean
     * @throws \Exception
     */
    public function isCanEdit(BuyContractSettlement $entity = null)
    {
        try
        {
            if(!empty($entity)){
                return $entity->isCanEdit();
            }else{
                return false;
            }
            
        } catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @desc 是否可提交
     * @param BuyContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function isCanSubmit(BuyContractSettlement $entity)
    {
        try
        {
            return $entity->isCanSubmit();
            
        } catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @desc 提交操作
     * @param BuyContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function submit(BuyContractSettlement $entity)
    {
        try
        {
            return $entity->submit();
            
        } catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @desc 审核完成
     * @param BuyContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkDone(BuyContractSettlement $entity)
    {
        try
        {
            return $entity->checkPass();
            
        } catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @desc 审核驳回
     * @param BuyContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkBack(BuyContractSettlement $entity)
    {
        try
        {
            return $entity->checkBack();
            
        } catch (\Exception $e)
        {
            throw $e;
        }
    }
}