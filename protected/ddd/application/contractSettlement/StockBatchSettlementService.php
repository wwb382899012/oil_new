<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：入库通知单结算
 */

namespace ddd\application\contractSettlement;

use ddd\domain\entity\stock\LadingBill;
use ddd\application\dto\contractSettlement\StockInBatchSettlementDTO;
use ddd\application\dto\stock\LadingBillDTO;
use ddd\Common\Application\TransactionService;
use ddd\domain\service\stock\LadingBillService;
use ddd\domain\entity\contractSettlement\LadingBillSettlement;
use ddd\repository\stock\LadingBillRepository;
use ddd\repository\contractSettlement\LadingBillSettlementRepository;



class StockBatchSettlementService extends TransactionService
{

    protected $LadingBillRepository;
    protected $LadingBillSettlementRepository;

    public function __construct()
    {
        $this->LadingBillRepository = new LadingBillRepository();
        $this->LadingBillSettlementRepository = new LadingBillSettlementRepository();
    }
    /**
     * @desc 获取入库通知单结算 信息
     * @param $batch_id 入库通知单id
     * @return 结算单对象
     * @throws \Exception
     */
    public function getStockBatchSettlement($batch_id){
        $batchInBatchEntity = LadingBillRepository::repository()->findByPk($batch_id);
        $stockBatchSettlementEntity = LadingBillSettlementRepository::repository()->find('t.lading_id='.$batch_id);
        if(empty($stockBatchSettlementEntity))
            $stockBatchSettlementEntity = $this->createStockBatchSettlement($batchInBatchEntity);
        //有入库通知单结算
        if(!is_string($stockBatchSettlementEntity)) {
            $StockInBatchSettlementDTO = new StockInBatchSettlementDTO();
            $StockInBatchSettlementDTO->fromEntity($stockBatchSettlementEntity);

            //$a = new StockBatchSettlementAssemble();
            //print_r($a->assemble($StockInBatchSettlementDTO)); //展示层接口

            return $StockInBatchSettlementDTO->getAttributes();
        }
        else
            return $stockBatchSettlementEntity;//返回异常

    }
    /**
     * @desc 对DTO进行赋值
     * @param $post=array(
     *  'settle_date'=>'',
     *  'settle_status'=>'',
     *  'goods_arr'=>[]
     * )
     * @return DTO
     * @throws \Exception
     */
    public function AssignDTO($batch_id,$post){
        $batchInBatchEntity = LadingBillRepository::repository()->findByPk($batch_id);
        $stockBatchSettlementEntity = LadingBillSettlementRepository::repository()->find('t.lading_id='.$batch_id);
        $isFirstSettle=true;//第一次保存
        if(empty($stockBatchSettlementEntity))
            $stockBatchSettlementEntity = $this->createStockBatchSettlement($batchInBatchEntity);
        else{
            $isFirstSettle=false;//不是第一次保存
            if($stockBatchSettlementEntity->status >= \ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_SUBMIT)
                return \BusinessError::outputError(\OilError::$STOCK_BATCH_SETTLE_NOT_ALLOW_EDIT);

        }

        //有入库通知单结算
        if(!is_string($stockBatchSettlementEntity)) {
            $StockInBatchSettlementDTO = new StockInBatchSettlementDTO();
            $StockInBatchSettlementDTO->fromEntity($stockBatchSettlementEntity);
            //重新赋值
            $SettleService = new \ddd\application\contractSettlement\SettleService();
            $StockInBatchSettlementDTO = $SettleService->createNewDTO($StockInBatchSettlementDTO,$post,$isFirstSettle);
            return $StockInBatchSettlementDTO;//返回赋值后的DTO
        }
        else
            return $stockBatchSettlementEntity;//返回异常
    }

    /**
     * @desc 创建结算单对象
     * @param LadingBillDTO $LadingBill
     * @return 结算单对象
     * @throws \Exception
     */
    public function createStockBatchSettlement(LadingBill $LadingBill){

        try
        {
            return LadingBillSettlement::create($LadingBill);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }

    }
    /**
     * 保存结算单（入库通知单结算）
     * @param LadingBillDTO $ladingBill
     * @param Contract|null $contract
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function saveLadingBillSettlement(StockInBatchSettlementDTO $StockInBatchSettlementDTO)
    {
        //数据格式验证，规则：保存数据时验证，暂存数据时不验证。 
        if($StockInBatchSettlementDTO->settle_status==2){
            if(!$StockInBatchSettlementDTO->validate()){
                return $StockInBatchSettlementDTO->getErrors();//验证不通过
            } else{
                if(!empty($StockInBatchSettlementDTO->settlementGoods)){
                    foreach ($StockInBatchSettlementDTO->settlementGoods as $key=>$value){
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
            }
        }
        try
        {
            $entity=$StockInBatchSettlementDTO->toEntity();
            //print_r($entity);exit;
            $this->beginTransaction();
            $this->LadingBillSettlementRepository->store($entity);
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
     * @desc 是否可结算
     * @param LadingBillDTO $LadingBill
     * @return boolean
     * @throws \Exception
     */
    public function isCanSettle(LadingBillDTO $LadingBill)
    {
        $entity = $LadingBill->toEntity();
        try
        {
            $service = new LadingBillService();
            return $service->isCanSettle($entity);

        } catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @desc 是否可修改
     * @param LadingBillDTO $LadingBill
     * @return boolean
     * @throws \Exception
     */
    public function isCanEdit(LadingBillSettlement $entity=null)
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
     * @param LadingBillSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function isCanSubmit(LadingBillSettlement $entity)
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
     * @param LadingBillSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function submit(LadingBillSettlement $entity)
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
     * @param LadingBillSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkDone(LadingBillSettlement $entity)
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
     * @param LadingBillSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkBack(LadingBillSettlement $entity)
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