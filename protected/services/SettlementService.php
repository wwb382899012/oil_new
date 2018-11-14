<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/10 17:02
 * Describe：
 */
class SettlementService
{
    public static function updateSettlementStatus($settleId,$status,$oldStatus=null)
    {
        $obj=Settlement::model()->findByPk($settleId);
        if(empty($obj->settle_id))
            return "当前结算信息不存在！";
        if($oldStatus!==null && $obj->status!=$oldStatus)
        {
            return "当前结算信息原状态与条件状态不一致！";
        }
        if($obj->status!=$status)
        {
            //$obj->old_status=$obj->status;
            $obj->status=$status;
            //$obj->status_time= date("Y-m-d H:i:s");
            $obj->update_user_id=Utility::getNowUserId();
            $obj->update_time=date("Y-m-d H:i:s");
            $res=$obj->save();
            if($res===true)
                return 1;
            else
                return $res;
        }
        else
            return 1;
    }


    /**
     * 获取附件信息
     * @param $projectId
     * @return array
     */
    public static function getAttachment($projectId)
    {
        if(empty($projectId))
            return array();

        $sql="select * from t_project_attachment where project_id=".$projectId." 
                and status=1 and type>=200 and type<300
                 order by type asc";
        $data=Utility::query($sql);
        $attachments=array();
        foreach($data as $v)
        {
            $attachments[$v["type"]]=$v;
        }
        return $attachments;
    }

    /**
     * 获取放款计划最后一期的日期
     */
    public static function getMaxPeroid($projectId)
    {
        $sql    = "select plan_id,period,pay_date from t_project_pay_plan where pay_type=2 and type=0 and project_id=".$projectId." order by pay_date desc limit 1";
        $data   = Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            return $data[0];
        }
        return array();
    }


    /**
     * 上游结算归档审核完，生成多退少补的打款信息
     * @param $projectId
     * @param $balanceAmount
     */
    public static function generateSettlePayment($projectId,$balanceAmount)
    {
        $type=0;
        $data=SettlementService::getMaxPeroid($projectId);
        $sql = "";
        if(!empty($data)){
            $pay_date = "'".$data['pay_date']."'";
        }else{
            $pay_date = 'now()';
        }
        if($balanceAmount>0){
            $type=1;
            $sql = "delete from t_project_pay_plan where project_id=".$projectId." and pay_type=2 and type=0 and status<2;";
        }else if($balanceAmount<0){
            $type=0;
            $balanceAmount=$balanceAmount*-1;
        }
        if($balanceAmount!=0)
        {
            $sql .= "insert into t_project_pay_plan(project_id,pay_date,type,pay_type,amount,create_time,update_time,status_time) values "
                   . "(" . $projectId . ",".$pay_date. $type . ",2," . $balanceAmount . ",now(),now(),now())";
            Utility::execute($sql);
        }
    }

    /**
     * 完成上游结算单的归档审核
     * @param $settleId
     * @return bool
     * @throws Exception
     */
    public static function doneUpSettleFile($settleId)
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $trans=Utility::beginTransaction();
        }

        try
        {
            /*$obj=Settlement::model()->findByPk($settleId);
            if(!empty($obj->settle_id))
            {
                self::generateSettlePayment($obj->project_id,$obj->balance_amount);
            }*/
            return true;
        }
        catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return false;
            }
            else
                throw $e;
        }
    }

    /**
     * 更新下游结算发票状态
     * @param $settleId
     * @return bool
     * @throws Exception
     */
    public static function updateSettleInvoiceStatus($projectId,$status)
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $trans=Utility::beginTransaction();
        }

        try
        {
            $sql = "update t_settlement set invoice_status=".$status." where type=2 and project_id=".$projectId;
            Utility::execute($sql);

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        }
        catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return false;
            }
            else
                throw $e;
        }
    }

    /**
     * 获取结清状态下收付款凭证附件
     */
    public static function getPayAttachment($planId,$projectId,$pType=0,$payType,$caType){
        $table      = "t_pay_attachment";
        $plan_id    = "plan_id";
        if($caType==1){
            if($pType>0)
                $type = 121;
            else
                $type = 21;

        }else{
            if($payType==1){
                $type=1;
                $table      = "t_rent_attachment";
                $plan_id    = "relation_id";
            }else{
                $type=51;
                $table = "t_remind_attachment";
            }
        }
        $sql = "select * from ".$table." where type=".$type." and status>0 and ".$plan_id."=".$planId." and project_id=".$projectId." order by id ";
        $data = Utility::query($sql);
        return $data;
    }



    /**
     * 结清确认时更新每条还款/回款计划确认状态
     * @param $planId
     * @param $content
     * @return bool
     * @throws Exception
     */
    public static function updatePlanConfirmStatus($planId,$content,$type)
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $trans=Utility::beginTransaction();
        }

        try
        {
            if($type==1){
                $table      = "t_project_pay_plan";
                $plan_id    = "plan_id";
            }else{
                $table      = "t_return_plan";
                $plan_id    = "id";
            }
            $sql = "update ".$table." set content='".$content."' where ".$plan_id."=".$planId;
            Utility::execute($sql);

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        }
        catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return false;
            }
            else
                throw $e;
        }
    }
    /**
     * 合同结算、入库通知单结算、发放单结算： 保存操作时数据转换
     * @param $arr
     * @param 1是入库通知单、采购合同结算；  2是发货单、销售合同结算
     * @return bool
     * @throws Exception
     */
    static public function dataConvert($arr,$type=1){
        $post=array(
            'settle_date'=>$arr['settle_date'],
            'settle_status'=>$arr['save_status'],
            'remark'=>$arr['remark'],
            'other_amount'=>$arr['other_amount'],
            'goods_amount'=>$arr['goods_amount'],
        );
        $not_goods_arr=array();
        if(!empty($arr['otherExpenseItems'])){
            foreach ($arr['otherExpenseItems'] as $key=>$value) {
                $child=array(
                    'detail_id'=>$value['detail_id'],
                    'amount'=>$value['amount'],
                    'exchange_rate'=>$value['exchange_rate'],
                    'amount_cny'=>$value['amount_cny'],
                    'remark'=>$value['remark'],
                    'currency'=>$value['currency'],
                    'fee'=>$value['subject_id'],
                    'otherFiles'=>$value['otherFiles']
                );
                $not_goods_arr[]=$child;
            }
        }
        $post['not_goods_arr']=$not_goods_arr;
        //货款
        $goods_arr=array();
        if(!empty($arr['goodsItems'])){
            foreach ($arr['goodsItems'] as $key=>$value) {
                $child=array(
                    'item_id'=>$value['item_id'],
                    'goods_id'=>$value['goods_id'],
                    'quantity'=>$value['quantity'],
                    'quantity_loss'=>$value['quantity_loss'],
                    'price'=>$value['price'],
                    'amount'=>$value['amount'],
                    'unit_rate'=>$value['exchange_rate'],
                    'price_cny'=>$value['price_cny'],
                    'amount_cny'=>$value['amount_cny'],
                    'hasDetail'=>$value['hasDetail'],
                    'remark'=>$value['remark'],
                );
                //ladbing_items
                $child['lading_items']=array();
                if(!empty($value['billItems'])&&$type==1){
                    foreach ($value['billItems'] as $k=>$v){
                        $temp=array(
                            'batch_id'=>$v['bill_id'],
                            'quantity'=>$v['quantity'],
                            'quantity_loss'=>$v['quantity_loss'],
                            'price'=>$v['price'],
                            'amount'=>$v['amount'],
                            'price_cny'=>$v['price_cny'],
                            'amount_cny'=>$v['amount_cny'],
                            'in_quantity'=>$v['bill_quantity']
                        );
                        $child['lading_items'][]=$temp;
                    }
                }
                //order_items
                $child['order_items']=array();
                if(!empty($value['billItems'])&&$type==2){
                    foreach ($value['billItems'] as $k=>$v){
                        $temp=array(
                            'order_id'=>$v['bill_id'],
                            'quantity'=>$v['quantity'],
                            'quantity_loss'=>$v['quantity_loss'],
                            'price'=>$v['price'],
                            'amount'=>$v['amount'],
                            'price_cny'=>$v['price_cny'],
                            'amount_cny'=>$v['amount_cny'],
                            'out_quantity'=>$v['bill_quantity']
                        );
                        $child['order_items'][]=$temp;
                    }
                }
                //录入明细
                $child['settlementGoodsDetail']=array();
                $child['settlementGoodsDetail']['currency']=$value['detail']['currency'];
                $child['settlementGoodsDetail']['amount_currency']=$value['detail']['amount_currency'];
                $child['settlementGoodsDetail']['exchange_rate']=$value['detail']['exchange_rate'];
                $child['settlementGoodsDetail']['amount_goods']=$value['detail']['amount_goods'];
                $child['settlementGoodsDetail']['price_goods']=$value['detail']['price_goods'];
                $child['settlementGoodsDetail']['exchange_rate_tax']=$value['detail']['exchange_rate_tax'];
                $child['settlementGoodsDetail']['amount_goods_tax']=$value['detail']['amount_goods_tax'];
                $child['settlementGoodsDetail']['adjust_type']=$value['detail']['adjust_type'];
                $child['settlementGoodsDetail']['amount_adjust']=$value['detail']['amount_adjust'];
                $child['settlementGoodsDetail']['reason_adjust']=$value['detail']['reason_adjust'];
                $child['settlementGoodsDetail']['quantity']=$value['detail']['quantity'];
                $child['settlementGoodsDetail']['quantity_actual']=isset($value['detail']['quantity_actual']['quantity'])?$value['detail']['quantity_actual']['quantity']:0;
                $child['settlementGoodsDetail']['price']=$value['detail']['price'];
                $child['settlementGoodsDetail']['price_actual']=$value['detail']['price_actual'];
                $child['settlementGoodsDetail']['amount']=$value['detail']['amount'];
                $child['settlementGoodsDetail']['amount_actual']=$value['detail']['amount_actual'];
                //税收
                $child['settlementGoodsDetail']['tax_detail_item']=array();
                if(!empty($value['detail']['taxItems']))
                {
                    foreach ($value['detail']['taxItems'] as $tax=>$tax_value){
                        $tax_new=array(
                            'subject_list'=>$tax_value['subject_id'],
                            'rate'=>$tax_value['rate'],
                            'price'=>$tax_value['price'],
                            'amount'=>$tax_value['amount'],
                            'remark'=>$tax_value['remark'],
                        );
                        $child['settlementGoodsDetail']['tax_detail_item'][]=$tax_new;
                    }
                }
                //其他费用
                $child['settlementGoodsDetail']['other_detail_item']=array();
                if(!empty($value['detail']['otherExpenseItems']))
                {
                    foreach ($value['detail']['otherExpenseItems'] as $other=>$other_value){
                        $other_new=array(
                            'subject_list'=>$other_value['subject_id'],
                            'price'=>$other_value['price'],
                            'amount'=>$other_value['amount'],
                            'remark'=>$other_value['remark'],
                        );
                        $child['settlementGoodsDetail']['other_detail_item'][]=$other_new;
                    }
                    
                }
                
                $goods_arr[]=$child;
            }
        }
        $post['goods_arr']=$goods_arr;
        
        return $post;
        
    }

}