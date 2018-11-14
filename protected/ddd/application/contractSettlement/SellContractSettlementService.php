<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：销售合同结算
 */

namespace ddd\application\contractSettlement;

use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\contractSettlement\SaleContractSettlement;
use ddd\domain\entity\contractSettlement\OtherExpenseSettlementItem;
use ddd\domain\entity\contractSettlement\SettlementStatus;
//use ddd\domain\service\contract\ContractService;
use ddd\application\dto\contractSettlement\SellContractSettlementDTO;
use ddd\application\dto\contractSettlement\ContractDTO;
use ddd\presentation\assemble\settlement\SellContractSettlementAssemble;
use ddd\Common\Application\TransactionService;
use ddd\repository\contract\ContractRepository;
use ddd\repository\contractSettlement\SaleContractSettlementRepository;

class SellContractSettlementService extends TransactionService
{

    protected $ContractRepository;
    protected $SaleContractSettlementRepository;

    public function __construct()
    {
        $this->ContractRepository = new ContractRepository();
        $this->SaleContractSettlementRepository = new SaleContractSettlementRepository();
    }
    /**
     * @desc 获取销售合同结算 信息
     * @param $contract_id 合同id
     * @return 结算单对象
     * @throws \Exception
     */
    public function getSellContractSettlement($contract_id){
        $contractEntity = ContractRepository::repository()->findByPk($contract_id);
        $sellContractSettlementEntity = SaleContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        if($sellContractSettlementEntity->status==\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW)
            $sellContractSettlementEntity = $this->createDeliveryContractSettlement($contractEntity);
        //有合同结算
        if(!is_string($sellContractSettlementEntity)) {
            $SellContractSettlementDTO = new SellContractSettlementDTO();
            $SellContractSettlementDTO->fromEntity($sellContractSettlementEntity);

            //$a = new SellContractSettlementAssemble();
            //print_r($a->assemble($SellContractSettlementDTO)); //展示层接口

            return $SellContractSettlementDTO->getAttributes();
        }
        else
            return $sellContractSettlementEntity;//返回异常
                
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
        $deliveryContractSettlementEntity = SaleContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        $isFirstSettle=true;//第一次保存
        if($deliveryContractSettlementEntity->status==\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW)
            $deliveryContractSettlementEntity = $this->createDeliveryContractSettlement($contractEntity);
        else{
            $isFirstSettle = false;
            if($deliveryContractSettlementEntity->status >= \ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_SUBMIT)
                return \BusinessError::outputError(\OilError::$SELL_CONTRACT_SETTLE_NOT_ALLOW_EDIT);
        }

        if(!is_string($deliveryContractSettlementEntity)) {
            $SellContractSettlementDTO = new SellContractSettlementDTO();
            $SellContractSettlementDTO->fromEntity($deliveryContractSettlementEntity);
            //赋值
            $SettleService = new \ddd\application\contractSettlement\SettleService();
            $SellContractSettlementDTO = $SettleService->createNewDTO($SellContractSettlementDTO,$post,$isFirstSettle);
            return $SellContractSettlementDTO;
        }
        else
            return $deliveryContractSettlementEntity;

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
    public function createDeliveryContractSettlement(Contract $Contract){
        try
        {
            return SaleContractSettlement::create($Contract);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        } 
    }
    /**
     * 保存结算单（采购合同结算）
     * @param SellContractSettlementDTO $SellContractSettlementDTO
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function saveDeliveryContractSettlement(SellContractSettlementDTO $SellContractSettlementDTO)
    { 
        //数据格式验证，规则：保存数据时验证，暂存数据时不验证。
        if($SellContractSettlementDTO->settle_status==2){
            if(!$SellContractSettlementDTO->validate()){
                return $SellContractSettlementDTO->getErrors();//验证不通过
            } else{
                if(!empty($SellContractSettlementDTO->settlementGoods)){
                    foreach ($SellContractSettlementDTO->settlementGoods as $key=>$value){
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
                if(!empty($SellContractSettlementDTO->other_expense)){
                    foreach ($SellContractSettlementDTO->other_expense as $p=>$q){
                        if(!$q->validate()){
                            return $q->getErrors();//验证不通过
                        }
                    }
                }//非货款金额
            }
        }
       
        try
        {
            $entity=$SellContractSettlementDTO->toEntity();
            $this->beginTransaction();
            //print_r($entity);die;
            $this->SaleContractSettlementRepository->store($entity);
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
     * 发起 销售合同结算审核流程
     * @param $BuyContractSettlementEntity
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function startFlow($SaleContractSettlementEntity){
        try {
            if ($SaleContractSettlementEntity->status == SettlementStatus::STATUS_SUBMIT) {
                \FlowService::startFlowForCheck22($SaleContractSettlementEntity->contract_id);
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
    public function isCanEdit(SaleContractSettlement $entity = null)
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
     * @param SaleContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function isCanSubmit(SaleContractSettlement $entity)
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
     * @param SaleContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function submit(SaleContractSettlement $entity)
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
     * @param SaleContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkDone(SaleContractSettlement $entity)
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
     * @param SaleContractSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkBack(SaleContractSettlement $entity)
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