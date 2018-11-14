<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:14
 * Describe：入库通知单结算
 */

namespace ddd\application\contractSettlement;
use ddd\application\dto\stock\DeliveryOrderDTO;
use ddd\application\dto\contractSettlement\DeliveryOrderSettlementDTO;
use ddd\presentation\assemble\settlement\DeliveryOrderSettlementAssemble;
use ddd\domain\entity\stock\DeliveryOrder;
use ddd\domain\entity\contractSettlement\DeliveryOrderSettlement;
use ddd\domain\service\stock\DeliveryOrderService;
use ddd\Common\Application\TransactionService;
use ddd\repository\stock\DeliveryOrderRepository;
use ddd\repository\contractSettlement\DeliveryOrderSettlementRepository;

class DeliveryOrderSettlementService extends TransactionService
{

    protected $DeliveryOrderRepository;
    protected $DeliveryOrderSettlementRepository;

    public function __construct()
    {
        $this->DeliveryOrderRepository = new DeliveryOrderRepository();
        $this->DeliveryOrderSettlementRepository = new DeliveryOrderSettlementRepository();
    }
    /**
     * @desc 获取发货单结算 信息
     * @param $order_id 发货单id
     * @return 结算单对象
     * @throws \Exception
     */
    public function getDeliveryOrderSettlement($order_id){
        $deliveryOrderEntity = DeliveryOrderRepository::repository()->findByPk($order_id);
        $deliveryOrderSettlementEntity = DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$order_id);
        if(empty($deliveryOrderSettlementEntity))
            $deliveryOrderSettlementEntity = $this->createDeliveryOrderSettlement($deliveryOrderEntity);
        //有发货单结算
        if(!is_string($deliveryOrderSettlementEntity)) {
            $DeliveryOrderSettlementDTO = new DeliveryOrderSettlementDTO();
            $DeliveryOrderSettlementDTO->fromEntity($deliveryOrderSettlementEntity);

            //$a = new DeliveryOrderSettlementAssemble();
            //print_r($a->assemble($DeliveryOrderSettlementDTO)); //展示层接口

            return $DeliveryOrderSettlementDTO->getAttributes();
        }
        else
            return $deliveryOrderSettlementEntity;//返回异常
                
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
    public function AssignDTO($order_id,$post){
        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($order_id);
        $deliveryOrderSettlementEntity = \ddd\repository\contractSettlement\DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$order_id);
        $isFirstSettle=true;//第一次保存
        if(empty($deliveryOrderSettlementEntity))
            $deliveryOrderSettlementEntity = $this->createDeliveryOrderSettlement($deliveryOrderEntity);
        else{
            $isFirstSettle = false;
            if($deliveryOrderSettlementEntity->status >= \ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_SUBMIT)
                return \BusinessError::outputError(\OilError::$DELIVERY_ORDER_SETTLE_NOT_ALLOW_EDIT);
        }
        if(!is_string($deliveryOrderSettlementEntity)) {
            $DeliveryOrderSettlementDTO = new \ddd\application\dto\contractSettlement\DeliveryOrderSettlementDTO();
            $DeliveryOrderSettlementDTO->fromEntity($deliveryOrderSettlementEntity);//转换为DTO对象

            //赋值
            $SettleService = new \ddd\application\contractSettlement\SettleService();
            $DeliveryOrderSettlementDTO = $SettleService->createNewDTO($DeliveryOrderSettlementDTO,$post,$isFirstSettle);
            return $DeliveryOrderSettlementDTO;
        }
        else
            return $deliveryOrderSettlementEntity;
    }

    /**
     * @desc 创建结算单对象
     * @param DeliveryOrder $DeliveryOrder
     * @return 结算单对象
     * @throws \Exception
     */
    public function createDeliveryOrderSettlement(DeliveryOrder $DeliveryOrder){
        try
        {
            return DeliveryOrderSettlement::create($DeliveryOrder);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        } 
    }
    /**
     * 保存结算单（发货单结算）
     * @param DeliveryOrderSettlementDTO $DeliveryOrderSettlementDTO
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function saveDeliveryOrderSettlement(DeliveryOrderSettlementDTO $DeliveryOrderSettlementDTO)
    {
        //数据格式验证，规则：保存数据时验证，暂存数据时不验证。
        if($DeliveryOrderSettlementDTO->settle_status==2){
            if(!$DeliveryOrderSettlementDTO->validate()){
                return $DeliveryOrderSettlementDTO->getErrors();//验证不通过
            } else{
                if(!empty($DeliveryOrderSettlementDTO->settlementGoods)){
                    foreach ($DeliveryOrderSettlementDTO->settlementGoods as $key=>$value){
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
            $entity=$DeliveryOrderSettlementDTO->toEntity();
            //print_r($entity);die;
            $this->beginTransaction();
            $this->DeliveryOrderSettlementRepository->store($entity);
            $this->commitTransaction();
            return true;
        }
        catch (Exception $e)
        {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }
    /**
     * @desc 是否可结算
     * @param DeliveryOrderDTO $DeliveryOrder
     * @return boolean
     * @throws \Exception
     */
    public function isCanSettle(DeliveryOrderDTO $DeliveryOrder)
    {
        $entity = $DeliveryOrder->toEntity();
        try
        {
            $service = new DeliveryOrderService();
            return $service->isCanSettle($entity);
            
        } catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @desc 是否可修改
     * @param DeliveryOrderDTO $DeliveryOrder
     * @return boolean
     * @throws \Exception
     */
    public function isCanEdit(DeliveryOrderSettlement $entity = null)
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
     * @param DeliveryOrderSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function isCanSubmit(DeliveryOrderSettlement $entity)
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
     * @param DeliveryOrderSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function submit(DeliveryOrderSettlement $entity)
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
     * @param DeliveryOrderSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkDone(DeliveryOrderSettlement $entity)
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
     * @param DeliveryOrderSettlement $entity
     * @return boolean
     * @throws \Exception
     */
    public function checkBack(DeliveryOrderSettlement $entity)
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