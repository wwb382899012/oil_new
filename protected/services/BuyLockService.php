<?php
/**
 * Created by vector.
 * DateTime: 2017/10/10 15:38
 * Describe：
 */

class BuyLockService
{
    //根据合同交易明细detailId判断是否有锁价明细或转月记录
    public static function isHaveLockDetail($detailId)
    {
        if(empty($detailId))
            return false;
        $contractGoods = ContractGoods::model()->with('lockPriceDetail','contractGoodsRollover')->findByPk($detailId);
        $lockDetail = $contractGoods->lockPriceDetail;
        $rollDetail = $contractGoods->contractGoodsRollover;
        // print(count($lockDetail));die;
        if(count($lockDetail)>0 || count($rollDetail)>0)
            return true;

        return false;
    }

    //根据合同contractId和品名goodsId获取锁价详细记录
    public static function getLockDetail($contractId, $goodsId)
    {
        $data = array();
        if(empty($contractId) || empty($goodsId))
            return $data;
        $data = LockPriceDetail::model()->findAllToArray(array("condition"=>"contract_id=".$contractId." and goods_id=".$goodsId,"order"=>"detail_id asc"));

        if(Utility::isNotEmpty($data)){
            $total_price    = 0;
            $total_quantity = 0;
            $total_amount   = 0;
            foreach ($data as $key => $value) {
                $target = ContractGoodsTarget::model()->findByPk($value['target_id']);
                $data[$key]['target_name'] = $target->name;
                $total_quantity += $value['quantity'];
                $total_amount   += $value['amount']*$value['quantity'];
            }
            $data['total_price']    = $total_amount/$total_quantity;
            $data['total_quantity'] = $total_quantity;
            $data['total_amount']   = $total_amount;
        }

        return $data;
    }

    //根据合同contractId和品名goodsId获取转月详细记录
    public static function getRolloverDetail($contractId, $goodsId)
    {
        $data = array();
         if(empty($contractId) || empty($goodsId))
            return $data;
        $data = ContractGoodsRollover::model()->findAllToArray(array("condition"=>"contract_id=".$contractId." and goods_id=".$goodsId,"order"=>"rollover_id asc"));
        if(Utility::isNotEmpty($data)){
            foreach ($data as $key => $value) {
                $target = ContractGoodsTarget::model()->findByPk($value['target_id']);
                $old_target = ContractGoodsTarget::model()->findByPk($value['old_target_id']);
                $data[$key]['target_name']      = $target->name;
                $data[$key]['old_target_name']  = $old_target->name;
            }
            
        }

        return $data;
    }

    //根据合同contract_id与品名goods_id获取所有计价标的
    public static function getAllTarget($contract_id, $goods_id)
    {
        $data = array();
        if(empty($contract_id) || empty($goods_id))
            return $data;
        $data = ContractGoodsTarget::model()->findAllToArray(array("condition"=>"contract_id=".$contract_id." and goods_id=".$goods_id,"order"=>"target_id asc"));

        return $data;
    }

    //检查必填参数
    public static function checkParamsValid($params, $type, $lock_type) {
        if (Utility::isNotEmpty($params) && Utility::checkQueryId($type) && Utility::checkQueryId($lock_type)) {
            $requiredParams = array('lock_id', 'goods_id', 'contract_id', 'project_id', 'quantity', 'unit', 'currency');
            if($type==ConstantMap::LOCK_PRICE){
                $requiredParams[] = 'target_id';
                $requiredParams[] = 'lock_date';
                $requiredParams[] = 'price_base';
            }else{
                $requiredParams[] = 'old_target_id';
                // $requiredParams[] = 'month_spread';
            }

            if($lock_type==ConstantMap::LOCK_PUT_ORDER){
                $requiredParams[] = "order_code";
                $requiredParams[] = "batch_id";
            }else{
                $requiredParams[] = "contract_code";
            }
            if (Utility::isNotEmpty($requiredParams)) {
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
                    return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
                }
            }
            return true;
        }

        return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
    }


    /**
     * 锁价
     * @param $lockId
     * @param $quantity
     * @param $order_index
     * @param $unit
     * @return bool
     */
    public static function lockPrice($lockId,$quantity,$order_index=0,$unit=0)
    {
        /*$rows=LockPrice::model()->updateByPk($lockId
            ,array("lock_quantity"=>new CDbExpression("lock_quantity+".$quantity),"update_time"=>new CDbExpression("now()"))
            ,"quantity-lock_quantity>=".$quantity
        );*/
        $params = array("lock_quantity"=>new CDbExpression("lock_quantity+".$quantity),"update_time"=>new CDbExpression("now()"));
        if($order_index==1)
            $params["unit"] = new CDbExpression($unit);
        $rows=LockPrice::model()->updateByPk($lockId,$params);
        if($rows==1)
        {
            return true;
        }
        else
            return false;

    }

    //插入按入库通知单锁价记录
    public static function insertLockStockNotice($data, $contractGoods, $userId=0)
    {
        if(empty($userId))
            $userId = Utility::getNowUserId();
        $sqls   = array();
        $stocks = array();
        foreach ($data as $key => $value) {
            $stocks[$value['batch_id']]['quantity'] += $value['quantity'];
            $stocks[$value['batch_id']]['unit'] = $value['unit'];
        }
        $values = array();
        if(count($stocks)>0){
            foreach ($stocks as $k => $val) {
                $values[] = '('.$contractGoods->detail_id.','.$k.','.$contractGoods->goods_id.','
                    .$contractGoods->contract_id.','.$val['quantity'].',0,'.$val['quantity']
                    .','.$val['unit'].',1,now(),'.$userId.',now(),'.$userId.',now()'.')';
            }
            $vs = array_chunk($values, 1000);
            foreach ($vs as $v)
            {
                $sql    = "insert into t_lock_price(detail_id,batch_id,goods_id,contract_id,quantity,lock_quantity,balance_quantity,unit,status,status_time,create_user_id,create_time,update_user_id,update_time) values ";
                $sql   .= implode(",", $v);
                $sqls[] = $sql;
            }
        }

        return $sqls;
    }


    /**
    * 根据合同contractId和品名goodsId判断是否有锁价记录
    * 如果有锁价记录，则按batch_id插入新的锁价记录
    * 如果没有，则不插入
    **/
    public static function insertLockRecord($batchId)
    {
        if(empty($batchId))
            return false;

        $stockNotice    = StockNoticeDetail::model()->with("contractGoods")->findAll("batch_id=".$batchId);
        $nowTime        = new CDbExpression("now()");
        $nowUserId      = Utility::getNowUserId();
        foreach ($stockNotice as $notice) {
            $contractGoods = $notice->contractGoods;
            if($contractGoods->lock_type==ConstantMap::LOCK_PUT_ORDER){
                $obj = new LockPrice();
                $obj->detail_id     = $contractGoods->detail_id;
                $obj->goods_id      = $contractGoods->goods_id;
                $obj->contract_id   = $contractGoods->contract_id;
                $obj->batch_id      = $notice->batch_id;
                $obj->quantity      = $notice->quantity;
                $obj->lock_quantity = 0;
                $obj->balance_quantity = $notice->quantity;
                $obj->unit = $notice->unit;
                $obj->status = 1;
                $obj->status_time   = $nowTime;
                $obj->create_user_id= $nowUserId;
                $obj->create_time   = $nowTime;
                $obj->update_time   = $nowTime;
                $obj->update_user_id= $nowUserId;
                $obj->save();
            }
        }

        return true;
    }

}