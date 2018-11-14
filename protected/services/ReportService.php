<?php

/**
 * Desc: 报表统计服务
 * User: wwb
 * Date: 2017/05/25 0009
 * Time: 15:03
 */

class ReportService
{

    /**
     * @desc 风控额度预警报表
     */
    public static function riskAmountWarning()
    {
        set_time_limit(0);
        $sql="select
                    a.*,d.id as partner_amount_warning_id,e.used_amount
              from
                    t_partner_apply a
                    left join t_partner as b on a.partner_id=b.partner_id
                    left join t_ownership c on c.id=b.ownership
                    left join t_partner_amount_warning d on d.partner_id = a.partner_id
                    left join t_partner_amount e on e.partner_id = a.partner_id and e.type=2
              where a.status =".PartnerApply::STATUS_PASS."
              group by a.partner_id order by a.status_time desc";
        $partnerList=Utility::query($sql);
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            self::createContractOverdue();
            if(Utility::isNotEmpty($partnerList)){
                foreach ($partnerList as $key=>$value) {
                    //if($value['partner_id']==524) {
                        $partnerAmountWarning = PartnerAmountWarning::model()->findByPk($value['partner_amount_warning_id']);
                        if (empty($partnerAmountWarning->id)) {
                            $partnerAmountWarning = new PartnerAmountWarning();
                        }

                        $partnerAmountWarning->partner_id = $value['partner_id'];
                        $partnerAmountWarning->name = $value['name'];
                        $partnerAmountWarning->level = $value['level'];
                        //第一次审核通过时间
                        $checkDetail_sql = "select
                                                min(a.update_time) as update_time
                                        from
                                                  t_check_detail a
                                                  left join t_partner_review r on a.obj_id=r.review_id
                                                  left join t_partner_apply p on r.partner_id=p.partner_id
                                        where a.business_id in (" . PartnerAmountWarning::BUSINESS_ID . ") and a.check_status=1 and a.status=1 and (a.obj_id='{$value['partner_id']}'  or p.partner_id='{$value['partner_id']}')        ";
                        $checkDetail = Utility::query($checkDetail_sql);

                        $partnerAmountWarning->join_time = empty($checkDetail[0]['update_time']) ? $value['status_time'] : $checkDetail[0]['update_time'];
                        $partnerAmountWarning->credit_amount = $value['credit_amount'];
                        $partnerAmountWarning->change_amount = 0;
                        //$partnerAmountWarning->change_reason = ;
                        $partnerAmountWarning->credit_total_amount = ($partnerAmountWarning->change_amount) + ($partnerAmountWarning->credit_amount);
                        //额度占用 = 额度动态报表的实际占用额度
                        //$actual_use = Utility::query(" select used_amount from t_partner_amount where partner_id = '{$value['partner_id']}' and type = 2");
                        $invoice_amount = self::getInvoiceAmount($value['partner_id']);//进项票金额
                        $partnerAmountWarning->actual_used_amount = $value['used_amount'] - $invoice_amount;
                        $partnerAmountWarning->available_amount = ($partnerAmountWarning->credit_total_amount) - ($partnerAmountWarning->actual_used_amount);
                        //逾期次数、最长逾期天数
                        $overdue = self::getPartnerOverdue($value['partner_id']);
                        $partnerAmountWarning->over_nums = $overdue['overdue_num'];
                        $partnerAmountWarning->max_over_days = $overdue['overdue_day'];
                        $partnerAmountWarning->status = $overdue['status'] < 0 ? '-1' : 0;
                        $partnerAmountWarning->save();
                    //}//if end
                }
            }
            $trans->commit();
            echo 'ok';
            return true;
        } catch(Exception $e) {
            $trans->rollback();
            echo $e->getMEssage();
            return false;
        }
    }
    /**
     * 风控额度预警报表:  获取合作方 的进项票金额
     * where条件： 进项票，货款类
     * */
    protected static function getInvoiceAmount($partner_id){
        $invoice =  Utility::query(" select
                                            ifnull(a.amount,0) as amount,a.status
                                      from   t_invoice_application a
                                            left join t_contract b on a.contract_id = b.contract_id
                                      where b.partner_id = '{$partner_id}' and a.type = ".ConstantMap::INPUT_INVOICE_TYPE." and a.type_sub = ".InvoiceApplication::SUB_TYPE_GOODS);
        $amount=0;
        if(!empty($invoice)){
            foreach($invoice as $key=>$value){
                if($value['status']==InvoiceApplication::STATUS_BACK){//驳回： 加
                    $amount=$amount-$value['amount'];
                }else{// 减
                    $amount=$amount+$value['amount'];
                }
            }
        }

        return $amount;
    }
    /**
     * 风控额度预警报表: 生成合同逾期数据
     * */
    protected static function createContractOverdue(){
        $expense_type = ContractOverdue::EXPENSE_TYPE_ONE.",".ContractOverdue::EXPENSE_TYPE_TWO.",".ContractOverdue::EXPENSE_TYPE_Three;
        $subject_list = ContractOverdue::SUBJECT_TYPE_ONE.",".ContractOverdue::SUBJECT_TYPE_SEVEN.",".ContractOverdue::SUBJECT_TYPE_EIGHT;
        $contract =  Utility::query(" select a.*,b.id as contract_overdue_id from  t_contract a left join t_contract_overdue b on a.contract_id = b.contract_id  where  a.type = ".ConstantMap::CONTRACT_CATEGORY_SUB_SALE." and a.status>=".Contract::STATUS_BUSINESS_CHECKED);
        if(!empty($contract)){
            foreach($contract as $key=>$value){
                //if($value['contract_id']=="1358") {
                $value['amount_cny']=$value['amount_cny']==0?1:$value['amount_cny'];
                $contractOverdue = ContractOverdue::model()->findByPk($value['contract_overdue_id']);
                if(empty($contractOverdue->id)){
                    $contractOverdue= new ContractOverdue();
                }
                $contractOverdue->contract_id = $value['contract_id'];
                $contractOverdue->partner_id = $value['partner_id'];
                $contractOverdue->contract_type = $value['type'];
                //计划收款时间
                $pay_plan_sql=" select pay_date from  t_payment_plan  where contract_id='{$value['contract_id']}' and expense_type in(" . $expense_type . ") order by pay_date desc limit 1";
                $pay_plan = Utility::query($pay_plan_sql);

                //收款总额
                $receive_confirm = Utility::query(" select ifnull(sum(a.amount),0) as amount from  t_receive_confirm a left join t_bank_flow b on a.flow_id = b.flow_id  where a.contract_id='{$value['contract_id']}' and a.status>=1 and a.subject in(".$subject_list.")");
                //所有收款
                $receive_list = Utility::query(" select a.receive_id,b.receive_date,a.amount from  t_receive_confirm a left join t_bank_flow b on a.flow_id = b.flow_id  where a.contract_id='{$value['contract_id']}' and a.status>=1 and a.subject in (".$subject_list.") order by b.receive_date asc ");

                if (empty($pay_plan)) {//没有收款计划，则不存在逾期
                    $contractOverdue->has_overdue = ContractOverdue::NO_OVERDUE;
                    $contractOverdue->overdue_day = 0;
                    $contractOverdue->status = ContractOverdue::STATUS_NORMAL;
                } else {

                    //是否逾期过
                    $t1 = strtotime($pay_plan[0]['pay_date']);//收款计划时间
                    $t2 = strtotime(date("Y-m-d"));//今天
                    $diff = round(($t2 - $t1) / 3600 / 24);//相差天数
                    //if($contractOverdue->has_overdue != ContractOverdue::HAS_OVERDUE){//没逾期过,才来计算，已逾期，没必要在计算
                    if ($diff > ContractOverdue::MONTH_DAY){//今天距离收款时间超过30天

                        $thirty_amount = self::getThirtyAmount($receive_list,$pay_plan[0]['pay_date']);
                        if($value['amount_cny']!=0 && ($thirty_amount/$value['amount_cny'] * 100) <= ContractOverdue::AMOUNT_OVERDUE_PERCENT) //30天以内的收款总额是否大于合同金额的 80%
                            $contractOverdue->has_overdue = ContractOverdue::HAS_OVERDUE;
                        else
                            $contractOverdue->has_overdue = ContractOverdue::NO_OVERDUE;
                    }else{
                        $contractOverdue->has_overdue = ContractOverdue::NO_OVERDUE;
                    }
                    //}
                    //当前状态：正常、催收
                    $receive_amount = empty($receive_confirm[0]['amount']) ? 0 : $receive_confirm[0]['amount'];//收款金额
                    $rate = ($receive_amount / $value['amount_cny']);
                    if(($rate * 100) > ContractOverdue::AMOUNT_OVERDUE_PERCENT){
                        $contractOverdue->status = ContractOverdue::STATUS_NORMAL;
                    }else{
                        if($diff >= ContractOverdue::MONTH_DAY)
                            $contractOverdue->status= ContractOverdue::STATUS_DUE;
                        else
                            $contractOverdue->status= ContractOverdue::STATUS_NORMAL;
                    }
                    //逾期结束时间、逾期天数
                    $sum_amount = 0;
                    $flag=true;
                    if(!empty($receive_list)){
                        foreach ($receive_list as $k=>$v) {

                            $sum_amount+=$v['amount'];
                            if($flag) {
                                if (($sum_amount / $value['amount_cny']) * 100 > ContractOverdue::AMOUNT_OVERDUE_PERCENT)
                                {
                                    $contractOverdue->overdue_endtime = $v['receive_date'];
                                    $flag = false;//停止计算
                                }
                                else {
                                    $contractOverdue->overdue_endtime = null;
                                }
                            }
                        }
                    }

                    $overdue_endtime = empty($contractOverdue->overdue_endtime)?date("Y-m-d"):$contractOverdue->overdue_endtime;
                    $overdue_day = round((strtotime($overdue_endtime) - $t1) / 3600 / 24);//差值

                    if($overdue_day >= ContractOverdue::MONTH_DAY){
                        $contractOverdue->overdue_day = $overdue_day - ContractOverdue::MONTH_DAY; //逾期天数
                        /*if(empty($contractOverdue->overdue_endtime)) {//截止今天，还是逾期情况
                            $contractOverdue->overdue_day = $overdue_day - ContractOverdue::MONTH_DAY; //逾期天数
                        }else{//交全款当天也算逾期
                            $contractOverdue->overdue_day = $overdue_day - ContractOverdue::MONTH_DAY+1; //逾期天数
                        }*/
                    }else{
                        $contractOverdue->overdue_day = 0; //逾期天数为0
                    }

                    $contractOverdue->save();

                }

                //}//if

            }

        }
    }
    /**
     * 风控额度预警报表: 获取30天内收款总额
     * */
    protected static function getThirtyAmount($receive_list,$start_date){
        $thirty_amount = 0;
        if(!empty($receive_list)){
            foreach($receive_list as $key=>$value){
                $t1 = strtotime($start_date);//收款计划时间
                $t2 = strtotime($value['receive_date']);//交款日期
                $diff = round(($t2 - $t1) / 3600 / 24);//相差天数
                if($diff<= ContractOverdue::MONTH_DAY){//30天之内的收款累加
                    $thirty_amount += $value['amount'];
                }
            }
        }
        return $thirty_amount;
    }
    /**
     * 风控额度预警报表:  获取合作方 的逾期次数和最长逾期天数
     * */
    protected static function getPartnerOverdue($partner_id){
        $part =  Utility::query(" select ifnull(sum(has_overdue),0) as overdue_num,sum(status) as status,ifnull(max(overdue_day),0) as overdue_day  from  t_contract_overdue where partner_id=".$partner_id);
        if(!empty($part))
            return $part[0];
    }
    /**
     * @desc 上游供应商报表
     */
    public static function partnerBuyContract()
    {
        set_time_limit(0);
        $sql="select
                    a.*,b.id as partner_buy_contract_id
              from
                    t_partner a
                    left join t_partner_buy_contract b on a.partner_id = b.partner_id
              where a.status =".Partner::STATUS_PASS." and FIND_IN_SET(".Partner::TYPE_UP.",a.type)
              order by a.partner_id asc";
        $partnerList=Utility::query($sql);
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            self::createBuyContractData();
            if(Utility::isNotEmpty($partnerList)){
                foreach ($partnerList as $key=>$value) {
                    //if($value['partner_id']==524) {
                        if(self::haveEffectiveContract($value['partner_id'],ConstantMap::BUY_TYPE)) {//存在有效计算的合同

                            $partnerBuy = PartnerBuyContract::model()->findByPk($value['partner_buy_contract_id']);
                            if (empty($partnerBuy->id)) {
                                $partnerBuy = new PartnerBuyContract();
                            }
                            $partnerBuy->partner_id = $value['partner_id'];
                            $partnerBuy->join_time = $value['create_time'];
                            $buy_contract_detail_sql="select
                                                             partner_id,sum(received_quantity) received_quantity,sum(not_received_quantity) not_received_quantity,sum(ontime_received_quantity) ontime_received_quantity,
                                                             sum(overdue_received_quantity) overdue_received_quantity,sum(contract_amount) contract_amount,sum(received_amount) received_amount,sum(not_received_amount) not_received_amount,
                                                             sum(pay_amount) pay_amount,sum(diff_amount) diff_amount,sum(invoice_quantity) invoice_quantity,sum(not_invoice_quantity_delivery) not_invoice_quantity_delivery,
                                                             sum(not_invoice_amount_delivery) not_invoice_amount_delivery,sum(not_invoice_quantity_contract) not_invoice_quantity_contract,sum(not_invoice_amount_contract) not_invoice_amount_contract,
                                                             sum(settle_quantity) settle_quantity,sum(settle_amount) settle_amount
                                                       from
                                                             t_partner_buy_contract_detail
                                                       where partner_id='{$value['partner_id']}'
                                                       group by partner_id
                                                       ";
                            $buy_contract_detail = Utility::query($buy_contract_detail_sql);

                            if(!empty($buy_contract_detail)) {
                                $partnerBuy->received_quantity = $buy_contract_detail[0]['received_quantity'];
                                $partnerBuy->not_received_quantity = $buy_contract_detail[0]['not_received_quantity'];
                                $partnerBuy->ontime_received_quantity = $buy_contract_detail[0]['ontime_received_quantity'];
                                $partnerBuy->overdue_received_quantity = $buy_contract_detail[0]['overdue_received_quantity'];
                                $partnerBuy->contract_amount = $buy_contract_detail[0]['contract_amount'];
                                $partnerBuy->received_amount = $buy_contract_detail[0]['received_amount'];
                                $partnerBuy->not_received_amount = $buy_contract_detail[0]['not_received_amount'];
                                $partnerBuy->pay_amount = $buy_contract_detail[0]['pay_amount'];
                                $partnerBuy->diff_amount = $buy_contract_detail[0]['diff_amount'];
                                $partnerBuy->invoice_quantity = $buy_contract_detail[0]['invoice_quantity'];
                                $partnerBuy->not_invoice_quantity_delivery = $buy_contract_detail[0]['not_invoice_quantity_delivery'];
                                $partnerBuy->not_invoice_amount_delivery = $buy_contract_detail[0]['not_invoice_amount_delivery'];
                                $partnerBuy->not_invoice_quantity_contract = $buy_contract_detail[0]['not_invoice_quantity_contract'];
                                $partnerBuy->not_invoice_amount_contract = $buy_contract_detail[0]['not_invoice_amount_contract'];
                                $partnerBuy->settle_quantity = $buy_contract_detail[0]['settle_quantity'];
                                $partnerBuy->settle_amount = $buy_contract_detail[0]['settle_amount'];

                            }

                            $partnerBuy->save();
                        }
                    //}//if end
                }
            }
            $trans->commit();
            echo 'success';
            return true;
        } catch(Exception $e) {
            $trans->rollback();
            echo $e->getMEssage();
            return false;
        }
    }
    /**
     * @desc 上游供应商报表: 是否有业务审核通过、主合同
     */
    protected static function haveEffectiveContract($partner_id,$contract_type)
    {
        $sql="select
                    a.contract_id
              from
                    t_contract a

              where  a.status >=".Contract::STATUS_BUSINESS_CHECKED." and a.type = ".$contract_type." and a.partner_id = ".$partner_id."
              ";
        $row=Utility::query($sql);
        if(Utility::isNotEmpty($row))
            return true;
        else
            return false;
    }
    /**
     * @desc 上游供应商报表: 统计合同数据
     */
    protected static function createBuyContractData()
    {
        $sql="select
                    a.*,b.id partner_buy_contract_detail_id
              from
                    t_contract a
                    left join t_partner_buy_contract_detail b on a.contract_id = b.contract_id
              where a.status >=".Contract::STATUS_BUSINESS_CHECKED." and a.type = ".ConstantMap::BUY_TYPE."
              order by a.contract_id asc";
        $contractList=Utility::query($sql);
        if(Utility::isNotEmpty($contractList)){
            foreach ($contractList as $key=>$value) {
                //if($value['contract_id']==1585) {//1016  1157
                $partnerBuyContractDetail = PartnerBuyContractDetail::model()->findByPk($value['partner_buy_contract_detail_id']);
                if (empty($partnerBuyContractDetail->id)) {
                    $partnerBuyContractDetail = new PartnerBuyContractDetail();
                }
                $partnerBuyContractDetail->partner_id = $value['partner_id'];
                $partnerBuyContractDetail->contract_id = $value['contract_id'];
                $partnerBuyContractDetail->contract_type = $value['type'];
                //交货数量、货值等
                $receiveArr = self::getReceiveArr($value['contract_id']);
                $partnerBuyContractDetail->received_quantity = $receiveArr['received_quantity'];
                $partnerBuyContractDetail->not_received_quantity = $receiveArr['not_received_quantity'];
                $partnerBuyContractDetail->ontime_received_quantity = $receiveArr['ontime_received_quantity'];
                $partnerBuyContractDetail->overdue_received_quantity = $receiveArr['overdue_received_quantity'];
                $partnerBuyContractDetail->received_amount = $receiveArr['received_amount'];
                $partnerBuyContractDetail->not_received_amount = $receiveArr['not_received_amount'];
                $partnerBuyContractDetail->contract_amount = $receiveArr['contract_amount'];
                //付款金额
                $partnerBuyContractDetail->pay_amount = self::getPayConfirmArr($value['contract_id']);
                $partnerBuyContractDetail->diff_amount = ($partnerBuyContractDetail->pay_amount)-($partnerBuyContractDetail->received_amount);
                //收票
                $invoiceArr = self::getInvoiceArr($value['contract_id'],$receiveArr['stock_in_list']);
                $partnerBuyContractDetail->invoice_quantity = $invoiceArr['invoice_quantity'];
                $partnerBuyContractDetail->not_invoice_quantity_delivery = $invoiceArr['not_invoice_quantity_delivery'];
                $partnerBuyContractDetail->not_invoice_amount_delivery = $invoiceArr['not_invoice_amount_delivery'];
                $partnerBuyContractDetail->not_invoice_quantity_contract = $invoiceArr['not_invoice_quantity_contract'];
                $partnerBuyContractDetail->not_invoice_amount_contract = $invoiceArr['not_invoice_amount_contract'];
                $partnerBuyContractDetail->settle_quantity = $invoiceArr['settle_quantity'];
                $partnerBuyContractDetail->settle_amount = $invoiceArr['settle_amount'];

                $partnerBuyContractDetail->save();
                //}//if end
            }
        }

    }
    /**
     * @desc 统计单个合同下已交货、未交货数量、逾期交货数量、准时交货数量、已交货货值、未交货货值
     */
    protected static function getReceiveArr($contract_id){
        $sql="
                select
                         a.*,b.unit contract_unit,b.unit_convert_rate,b.price contract_price,c.delivery_term,b.quantity contract_quantity,s.quantity quantity_sub,d.entry_date,
                         c.delivery_term,c.status contract_status,c.exchange_rate,c.currency contract_currency,c.amount_cny contract_amount_cny,s.unit sub_unit
                from
                         t_stock_in_detail a
                         left join t_contract_goods b on a.contract_id = b.contract_id and a.goods_id = b.goods_id
                         left join t_contract c on a.contract_id=c.contract_id
                         left join t_stock_in d on a.stock_in_id = d.stock_in_id
                         LEFT join t_stock_in_detail_sub s on a.stock_id = s.stock_id
                where a.contract_id = '{$contract_id}' and d.status>=".StockIn::STATUS_PASS."
             ";
        $stockInlist=Utility::query($sql);
        $return =array(
            'received_quantity'=>0,//已交货数量
            'not_received_quantity'=>0,//未交货数量
            'ontime_received_quantity'=>0,//准时交货数量
            'overdue_received_quantity'=>0,//逾期交货数量
            'received_amount'=>0,//已交货货值
            'not_received_amount'=>0, //未交货货值
            'contract_amount'=>0, //合同签约金额
            'stock_in_list'=>array()
        );
        $contract_goods = Utility::query("select a.quantity,a.unit,a.unit_convert_rate,b.amount_cny contract_amount_cny from t_contract_goods a left join t_contract b on a.contract_id=b.contract_id where a.contract_id=".$contract_id);
        $contract_goods_quantity =0;
        if(!empty($contract_goods)){
            foreach($contract_goods as $goods_key=>$goods_value){
                $contract_goods_quantity += ($goods_value['unit_convert_rate']=='0.0000'?$goods_value['quantity']:($goods_value['quantity']/$goods_value['unit_convert_rate'])); //单位为吨
                $return['contract_amount'] = $goods_value['contract_amount_cny'];
            }
        }
        $return['not_received_quantity'] = $contract_goods_quantity;

        if(!empty($stockInlist)){
            foreach($stockInlist as $key=>$value){
                $return['received_quantity'] += self::getQuantityTon($value);
                if($value['entry_date']<=$value['delivery_term']){
                    $return['ontime_received_quantity'] += self::getQuantityTon($value);
                }
                //已交货货值
                if($value['contract_currency']==ConstantMap::CURRENCY_RMB)
                    $return['received_amount'] += self::getQuantityTon($value)*($value['contract_price']*$value['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                else //若是美元，需要转换为人民币
                    $return['received_amount'] += self::getQuantityTon($value)*($value['contract_price']*$value['exchange_rate']*$value['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
            }
        }
        $return['not_received_quantity'] = $contract_goods_quantity - $return['received_quantity'];
        $return['overdue_received_quantity'] = $return['received_quantity'] - $return['ontime_received_quantity'];
        $return['not_received_amount'] = $return['contract_amount'] - $return['received_amount'];
        if($value['contract_status']>=Contract::STATUS_SETTLED){//合同已结算
            $return['received_quantity'] = self::getBuyContractQuantity($value['contract_id']);
            $return['not_received_quantity'] = 0;
            $return['not_received_amount'] = 0;
        }
        $return['stock_in_list'] = $stockInlist;
        return $return;
    }
    //返回计量单位为吨的数值
    protected static function getQuantityTon($value){
       $return =$value['quantity'];
        if($value['unit']==ConstantMap::UNIT_TON)
           $return = $value['quantity'];
        elseif($value['contract_unit']==ConstantMap::UNIT_TON){
           $return = $value['quantity_sub'];
        }else{
            if($value['unit']==$value['contract_unit']){//分2种情况
                if($value['sub_unit']==ConstantMap::UNIT_TON)
                    $return = $value['quantity_sub'];
                else
                    $return = $value['quantity']/$value['unit_convert_rate'];
            }
            else
                $return = ($value['quantity']/$value['unit_rate'])/$value['unit_convert_rate'];
        }

        return $return;
    }
    //获取合同结算数量,入库单结算的单位 = 采购合同单位
    protected static function getBuyContractQuantity($contract_id,$goods_id=''){
        $where='';
        if(!empty($goods_id))
            $where.=" and a.goods_id=".$goods_id;
        $sql="select
                                                a.quantity,a.unit,b.unit_convert_rate,d.unit batch_unit,e.unit batch_unit_sub,d.unit_rate
                                        from
                                                t_contract_settlement_goods a
                                                left join t_contract_goods b on a.contract_id=b.contract_id and a.goods_id=b.goods_id
                                                left join t_contract c on a.contract_id=c.contract_id
                                                left join t_stock_in_batch_detail d on a.relation_id=d.batch_id and d.goods_id=a.goods_id
                                                left join t_stock_in_batch_detail_sub e on e.detail_id=d.detail_id
                                        where a.contract_id=".$contract_id." {$where} and c.status=".Contract::STATUS_SETTLED;
        $settle_goods = Utility::query($sql);
        $quantity = 0;

        if(!empty($settle_goods)){
            foreach($settle_goods as $k=>$v){
                $v_quantity=0;
                if($v['unit']==ConstantMap::UNIT_TON){//单位为吨，不转换
                    $v_quantity=$v['quantity'];
                }elseif(!in_array(ConstantMap::UNIT_TON,array($v['batch_unit'],$v['batch_unit_sub']))){//入库通知单单位里没有吨,使用合同单位转换比
                    $v_quantity=$v['quantity']/$v['unit_convert_rate'];
                }else{//入库通知单单位里没有吨,使用入库通知单单位转换比
                    $v_quantity=$v['quantity']/$v['unit_rate'];
                }
                $quantity += $v_quantity; //单位为吨
            }
        }
        return $quantity;
    }
    //获取合同已付款:包括合同下付款、多合同付款、后补付款认领
    protected static function getContractPayment($contract_id,$subject_list){
        $amount=0;
        //1、合同下付款
        $payList = Utility::query( "
                select
                       a.amount_cny pay_amount_any from t_payment a left join t_pay_application b on a.apply_id=b.apply_id
                where b.contract_id = " . $contract_id ." and b.category=".PayApplication::CATEGORY_NORMAL." and b.type!=".PayApplication::TYPE_MULTI_CONTRACT." and b.subject_id in (".$subject_list.") and a.status = ".Payment::STATUS_SUBMITED."
               ");
        if(!empty($payList)){
            foreach ($payList as $k=>$v) {
                $amount += $v['pay_amount_any'];
            }
        }

        //2、多合同付款，按合同先后顺序均摊实付金额
        $amount2=0;
        $payLists = Utility::query( "
                select
                       sum(a.amount_cny) amount_cny,a.apply_id,c.detail_id,c.amount detail_amount from t_payment a left join t_pay_application b on a.apply_id=b.apply_id
                       left join t_pay_application_detail c on a.apply_id=c.apply_id
                where c.contract_id = " . $contract_id ." and b.category=".PayApplication::CATEGORY_NORMAL." and b.type=".PayApplication::TYPE_MULTI_CONTRACT." and b.subject_id in (".$subject_list.") and a.status = ".Payment::STATUS_SUBMITED."
                group by a.apply_id");
        if(!empty($payLists)){
            foreach ($payLists as $key=>$value) {

                //获取该合同前面的付款信息
                $payDetail= Utility::query("select * from t_pay_application_detail where apply_id=".$value['apply_id']." and detail_id<".$value['detail_id']);
                $beforeContractAmount=0;//该合同前面所有合同付款金额之和
                if(!empty($payDetail)){
                    foreach($payDetail as $pay_key=>$pay_value){
                        $beforeContractAmount += $pay_value['amount'];
                    }
                }
                //划分到该合同下的实付金额
                $amount2 += ($value['amount_cny']-$beforeContractAmount)>=$value['detail_amount']?$value['detail_amount']:$value['amount_cny']-$beforeContractAmount;

            }
        }
        //3、后补付款认领
        $amount3=0;
        $payClaim = InterestReportService::getClaimInfo($contract_id);
        if(!empty($payClaim)){
            foreach($payClaim as $claim_key=>$claim_value){
                $amount3 += $claim_value['amount_cny'];
            }
        }
        return $amount+$amount2+$amount3;
    }
    //获取合同已付款:包括合同下付款、多合同付款、后补付款认领
    public static function getContractPaymentNew($contract_id,$subject_list){
        $subject_arr=explode(",",$subject_list);
        $amount=0;
        //1、合同下付款
        $actualPay = InterestReportService::getActualPayInfo($contract_id);
        if(!empty($actualPay)){
            foreach($actualPay as $actual_key=>$actual_value){
                if(in_array($actual_value['subject_id'],$subject_arr))
                $amount += $actual_value['amount_cny'];
            }
        }
        //2、多合同付款，按合同先后顺序均摊实付金额
        $amount2=0;
        $multiPay = InterestReportService::getMultiContractActualPayInfo($contract_id);
        if(!empty($multiPay)){
            foreach($multiPay as $multi_key=>$multi_value){
                if(in_array($multi_value['subject_id'],$subject_arr))
                $amount2 += $multi_value['amount_cny'];
            }
        }
        //3、后补付款认领
        $amount3=0;
        $payClaim = InterestReportService::getClaimInfo($contract_id);
        if(!empty($payClaim)){
            foreach($payClaim as $claim_key=>$claim_value){
                if(in_array($claim_value['subject_id'],$subject_arr))
                $amount3 += $claim_value['amount_cny'];
            }
        }
        return $amount+$amount2+$amount3;
    }
    /**
     * @desc 统计单个合同下的付款金额:采购已实付金额之和 - 采购合同已认领金额之和
     */
    protected static function getPayConfirmArr($contract_id){
        $pay_amount = 0;
        $receive_amount = 0;
        $subject_list = ContractOverdue::SUBJECT_TYPE_ONE.",".ContractOverdue::SUBJECT_TYPE_SEVEN.",".ContractOverdue::SUBJECT_TYPE_EIGHT;
        if(!empty($contract_id)) {
            $contractInfo = Utility::query("select * from t_contract where contract_id = " . $contract_id);
            // 合同类型为“代理进口合同” 且 代理模式为“购销代理模式”，税款保证金 也算货款
            if($contractInfo[0]['category']==ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT and $contractInfo[0]['agent_type'] == ConstantMap::AGENT_TYPE_BUY_SALE)
                $subject_list = ContractOverdue::SUBJECT_TYPE_ONE.",".ContractOverdue::SUBJECT_TYPE_SIX.",".ContractOverdue::SUBJECT_TYPE_SEVEN.",".ContractOverdue::SUBJECT_TYPE_EIGHT;

            //付款：包括付款申请和后补认领
            $pay_amount = self::getContractPaymentNew($contract_id,$subject_list);

            //收款认款金额
            $receive_sql = "
                select
                       a.* from t_receive_confirm a

                where a.contract_id = " . $contract_id ." and a.subject in (".$subject_list.") and a.status >= ".ReceiveConfirm::STATUS_SUBMITED."
               ";
            $receiveList = Utility::query($receive_sql);
            if(!empty($receiveList)){
                foreach ($receiveList as $key=>$value) {
                    $receive_amount += $value['amount_cny'];
                }
            }
            return $pay_amount-$receive_amount;
        }
        return 0;
    }
    /**
     * @desc 统计单个合同下进项票情况
     */
    protected static function getInvoiceArr($contract_id,$stock_in_list){
        $return =array(
            'invoice_quantity'=>0,//已收票数量
            'not_invoice_quantity_delivery'=>0,//未收票数量，按交货
            'not_invoice_amount_delivery'=>0,//未收票金额，按交货
            'not_invoice_quantity_contract'=>0,//未收票数量，按合同
            'not_invoice_amount_contract'=>0,//未收票金额，按合同
            'settle_quantity'=>0,
            'settle_amount'=>0
        );
        $sql="
                select
                         a.*,c.unit_convert_rate
                from
                         t_invoice_application_detail a
                         left join t_invoice_application b on a.apply_id = b.apply_id
                         left join t_contract_goods c on c.contract_id = b.contract_id and c.goods_id = a.goods_id
                where b.contract_id = '{$contract_id}' and b.status=".InvoiceApplication::STATUS_PASS." and b.type_sub =".InvoiceApplication::SUB_TYPE_GOODS." and b.type =".InvoiceApplication::TYPE_BUY."
             ";
        $invoiceList=Utility::query($sql);
        $contract_settlement_list = Utility::query("select a.* from t_contract_settlement_goods a left join t_contract b on a.contract_id=b.contract_id where a.contract_id=".$contract_id." and b.status=".Contract::STATUS_SETTLED);
        $contract_goods = Utility::query("select a.contract_id,a.quantity,a.goods_id,a.price,a.unit_convert_rate,b.currency contract_currency,b.exchange_rate,b.status contract_status from t_contract_goods a left join t_contract b on a.contract_id = b.contract_id where a.contract_id=".$contract_id);
        if(!empty($contract_goods)){
            foreach($contract_goods as $k=>$v){//循环商品
                $v_invoice_quantity = 0;//每个商品的收票数量（吨）
                $v_invoice_amount = 0;//每个商品的收票金额（元）
                $v_received_quantity = 0; //每个商品的交货数量（吨）
                $v_not_invoice_quantity_delivery = 0; //每个商品的未收票数量（吨），按交货
                $v_not_invoice_amount_delivery = 0; //每个商品的未收票金额（人民币），按交货

                $v_not_invoice_quantity_contract = 0; //每个商品的未收票数量（吨），按合同
                $v_not_invoice_amount_contract = 0; //每个商品的未收票金额（吨），按合同

                if(!empty($invoiceList)){
                    foreach($invoiceList as $key=>$value){
                        if($v['goods_id']==$value['goods_id']) {
                            $v_invoice_quantity += $value['quantity'] / $value['unit_convert_rate'];//已收票数量
                            $v_invoice_amount += $value['amount'];
                        }

                    }
                }

                if(!empty($stock_in_list)){
                    foreach($stock_in_list as $stock_key=>$stock_value){
                        if($v['goods_id']==$stock_value['goods_id']){
                            $v_received_quantity += self::getQuantityTon($stock_value);
                        }
                    }
                }
                $v_not_invoice_quantity_delivery = $v_received_quantity-$v_invoice_quantity;
                if($v['contract_currency']==ConstantMap::CURRENCY_RMB)
                    $v_not_invoice_amount_delivery = $v_not_invoice_quantity_delivery*($v['price']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                else //若是美元，需要转换为人民币
                    $v_not_invoice_amount_delivery = $v_not_invoice_quantity_delivery*($v['price']*$v['exchange_rate']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价

                //未收票数量，按合同
                $v['unit_convert_rate'] = $v['unit_convert_rate']=='0.0000'?1:$v['unit_convert_rate'];
                $v_not_invoice_quantity_contract = ($v['quantity']/$v['unit_convert_rate'])-$v_invoice_quantity; //合同数量 - 已收票数量
                if($v['contract_currency']==ConstantMap::CURRENCY_RMB)
                    $v_not_invoice_amount_contract = $v_not_invoice_quantity_contract*($v['price']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                else //若是美元，需要转换为人民币
                    $v_not_invoice_amount_contract = $v_not_invoice_quantity_contract*($v['price']*$v['exchange_rate']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                //未收票数量，按合同——合同已结算
                $settle_quantity=0;
                $settle_amount=0;
                if($v['contract_status']==Contract::STATUS_SETTLED) {//合同已结算
                    if(!empty($contract_settlement_list)){
                        foreach($contract_settlement_list as $settle_key=>$settle_value){

                            if($v['goods_id']==$settle_value['goods_id']) {
                                //$settle_quantity += $settle_value['quantity']/$v['unit_convert_rate'];//吨
                                $settle_amount += $settle_value['amount_cny'];//人民币
                            }
                        }
                    }
                    $settle_quantity = self::getBuyContractQuantity($contract_id,$v['goods_id']);

                    $v_not_invoice_quantity_contract = $settle_quantity-$v_invoice_quantity; //结算数量 - 已收票数量
                    $v_not_invoice_amount_contract = $settle_amount-$v_invoice_amount;

                }

                $return['invoice_quantity'] += $v_invoice_quantity;//全部商品的收票数量之和
                $return['not_invoice_quantity_delivery'] += $v_not_invoice_quantity_delivery;//全部商品的未收票数量之和，按交货
                $return['not_invoice_amount_delivery'] += $v_not_invoice_amount_delivery;//全部商品的未收票金额之和，按交货
                $return['not_invoice_quantity_contract'] += $v_not_invoice_quantity_contract;//全部商品的未收票数量之和，按合同
                $return['not_invoice_amount_contract'] += $v_not_invoice_amount_contract;//全部商品的未收票金额之和，按合同
                $return['settle_quantity'] += $settle_quantity;//全部商品 结算数量
                $return['settle_amount'] += $settle_amount; //全部商品 金额
            }


        }

        $return['invoice_quantity'] = round ($return['invoice_quantity'],4);
        $return['not_invoice_quantity_delivery'] = round ($return['not_invoice_quantity_delivery'],4);
        $return['not_invoice_quantity_contract'] = round ($return['not_invoice_quantity_contract'],4);
        //var_dump($return);
        return $return;
    }

    /**
     * @desc 下游客户报表
     */
    public static function partnerSellContract()
    {
        set_time_limit(0);
        $sql="select
                    a.*,b.id as partner_sell_contract_id
              from
                    t_partner a
                    left join t_partner_sell_contract b on a.partner_id = b.partner_id
              where a.status =".Partner::STATUS_PASS." and FIND_IN_SET(".Partner::TYPE_DOWN.",a.type)
              order by a.partner_id asc";
        $partnerList=Utility::query($sql);
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            self::createSellContractData();
            if(Utility::isNotEmpty($partnerList)){
                foreach ($partnerList as $key=>$value) {
                    //if($value['partner_id']==524) {
                    if(self::haveEffectiveContract($value['partner_id'],ConstantMap::SALE_TYPE)) {//存在有效计算的合同

                        $partnerSell = PartnerSellContract::model()->findByPk($value['partner_sell_contract_id']);
                        if (empty($partnerSell->id)) {
                            $partnerSell = new PartnerSellContract();
                        }
                        $partnerSell->partner_id = $value['partner_id'];
                        $partnerSell->join_time = $value['create_time'];

                        $sell_contract_detail_sql="select
                                                             partner_id,sum(delivery_quantity) delivery_quantity,sum(not_delivery_quantity) not_delivery_quantity,sum(contract_amount) contract_amount,
                                                             sum(delivery_amount) delivery_amount,sum(not_delivery_amount) not_delivery_amount,sum(receive_amount) receive_amount,sum(not_receive_amount) not_receive_amount,
                                                             sum(invoice_quantity) invoice_quantity,sum(not_invoice_quantity_delivery) not_invoice_quantity_delivery,sum(not_invoice_amount_delivery) not_invoice_amount_delivery,
                                                             sum(not_invoice_quantity_contract) not_invoice_quantity_contract,sum(not_invoice_amount_contract) not_invoice_amount_contract,sum(settle_quantity) settle_quantity,
                                                             sum(settle_amount) settle_amount
                                                       from
                                                             t_partner_sell_contract_detail
                                                       where partner_id='{$value['partner_id']}'
                                                       group by partner_id
                                                       ";
                        $sell_contract_detail = Utility::query($sell_contract_detail_sql);

                        if(!empty($sell_contract_detail)) {
                            $partnerSell->delivery_quantity = $sell_contract_detail[0]['delivery_quantity'];
                            $partnerSell->not_delivery_quantity = $sell_contract_detail[0]['not_delivery_quantity'];
                            $partnerSell->contract_amount = $sell_contract_detail[0]['contract_amount'];
                            $partnerSell->delivery_amount = $sell_contract_detail[0]['delivery_amount'];
                            $partnerSell->not_delivery_amount = $sell_contract_detail[0]['not_delivery_amount'];
                            $partnerSell->receive_amount = $sell_contract_detail[0]['receive_amount'];
                            $partnerSell->not_receive_amount = $sell_contract_detail[0]['not_receive_amount'];
                            $partnerSell->invoice_quantity = $sell_contract_detail[0]['invoice_quantity'];
                            $partnerSell->not_invoice_quantity_delivery = $sell_contract_detail[0]['not_invoice_quantity_delivery'];
                            $partnerSell->not_invoice_amount_delivery = $sell_contract_detail[0]['not_invoice_amount_delivery'];
                            $partnerSell->not_invoice_quantity_contract = $sell_contract_detail[0]['not_invoice_quantity_contract'];
                            $partnerSell->not_invoice_amount_contract = $sell_contract_detail[0]['not_invoice_amount_contract'];
                            $partnerSell->settle_quantity = $sell_contract_detail[0]['settle_quantity'];
                            $partnerSell->settle_amount = $sell_contract_detail[0]['settle_amount'];

                        }

                        $partnerSell->save();
                    }
                    //}//if end
                }
            }
            $trans->commit();
            echo 'success';
            return true;
        } catch(Exception $e) {
            $trans->rollback();
            echo $e->getMEssage();
            return false;
        }
    }
    /**
     * @desc 下游客户报表: 统计销售合同数据
     */
    protected static function createSellContractData()
    {
        $sql="select
                    a.*,b.id partner_sell_contract_detail_id
              from
                    t_contract a
                    left join t_partner_sell_contract_detail b on a.contract_id = b.contract_id
              where a.status >=".Contract::STATUS_BUSINESS_CHECKED." and a.type = ".ConstantMap::SALE_TYPE."
              order by a.contract_id asc";
        $contractList=Utility::query($sql);
        if(Utility::isNotEmpty($contractList)){
            foreach ($contractList as $key=>$value) {
                //if($value['contract_id']==1180) {//1016  1157
                $partnerSellContractDetail = PartnerSellContractDetail::model()->findByPk($value['partner_sell_contract_detail_id']);
                if (empty($partnerSellContractDetail->id)) {
                    $partnerSellContractDetail = new PartnerSellContractDetail();
                }

                $partnerSellContractDetail->partner_id = empty($value['partner_id'])?0:$value['partner_id'];
                $partnerSellContractDetail->contract_id = $value['contract_id'];
                $partnerSellContractDetail->contract_type = $value['type'];
                //提货数量、货值等
                $deliveryArr = self::getDeliveryArr($value['contract_id']);
                $partnerSellContractDetail->delivery_quantity = $deliveryArr['delivery_quantity'];
                $partnerSellContractDetail->not_delivery_quantity = $deliveryArr['not_delivery_quantity'];
                $partnerSellContractDetail->contract_amount = $deliveryArr['contract_amount'];
                $partnerSellContractDetail->delivery_amount = $deliveryArr['delivery_amount'];
                $partnerSellContractDetail->not_delivery_amount = $deliveryArr['not_delivery_amount'];
                //收款金额
                $partnerSellContractDetail->receive_amount = self::getReceiveConfirmArr($value['contract_id']);
                $partnerSellContractDetail->not_receive_amount = $partnerSellContractDetail->delivery_amount - $partnerSellContractDetail->receive_amount;
                //开票数量、金额
                $invoiceArr = self::getInvoiceArr_sell($value['contract_id'],$deliveryArr['stock_out_list']);
                $partnerSellContractDetail->invoice_quantity = $invoiceArr['invoice_quantity'];
                $partnerSellContractDetail->not_invoice_quantity_delivery = $invoiceArr['not_invoice_quantity_delivery'];
                $partnerSellContractDetail->not_invoice_amount_delivery = $invoiceArr['not_invoice_amount_delivery'];
                $partnerSellContractDetail->not_invoice_quantity_contract = $invoiceArr['not_invoice_quantity_contract'];
                $partnerSellContractDetail->not_invoice_amount_contract = $invoiceArr['not_invoice_amount_contract'];
                $partnerSellContractDetail->settle_quantity = $invoiceArr['settle_quantity'];
                $partnerSellContractDetail->settle_amount = $invoiceArr['settle_amount'];

                $partnerSellContractDetail->save();
                //}//if end
            }
        }

    }
    /**
     * @desc 统计单个合同下已提货、未提货数量、已提货货值、未提货货值
     */
    protected static function getDeliveryArr($contract_id){
        $sql="
                select
                         a.*,b.unit contract_unit,b.unit_convert_rate,b.price contract_price,c.delivery_term,b.quantity contract_quantity,
                         c.delivery_term,c.status contract_status,c.exchange_rate,c.currency contract_currency,c.amount_cny contract_amount_cny,
                         e.unit stock_in_unit,f.unit stock_in_unit_sub,e.unit_rate,g.unit_convert_rate buy_unit_convert_rate
                from
                         t_stock_out_detail a
                         left join t_contract_goods b on a.contract_id = b.contract_id and a.goods_id = b.goods_id
                         left join t_contract c on a.contract_id=c.contract_id
                         left join t_stock_out_order d on a.out_order_id = d.out_order_id
                         left join t_stock_in_detail e on a.stock_id=e.stock_id
                         left join t_stock_in_detail_sub f on a.stock_id=f.stock_id
                         left join t_contract_goods g on g.contract_id = e.contract_id and g.goods_id = e.goods_id
                where a.contract_id = '{$contract_id}' and (d.status=".StockOutOrder::STATUS_SUBMITED." or d.status=".StockOutOrder::STATUS_SETTLED.")
             ";
        $stockOutlist=Utility::query($sql);
        $return =array(
            'delivery_quantity'=>0,//已提货数量
            'not_delivery_quantity'=>0,//未提货数量
            'delivery_amount'=>0,//已提货货值
            'not_delivery_amount'=>0, //未提货货值
            'contract_amount'=>0, //合同签约金额
            'stock_out_list'=>array()
        );
        $contract_goods = Utility::query("select a.quantity,a.unit,a.unit_convert_rate,b.amount_cny contract_amount_cny from t_contract_goods a left join t_contract b on a.contract_id=b.contract_id where a.contract_id=".$contract_id);
        $contract_goods_quantity =0;
        if(!empty($contract_goods)){
            foreach($contract_goods as $goods_key=>$goods_value){
                $contract_goods_quantity += ($goods_value['unit_convert_rate']=='0.0000'?$goods_value['quantity']:($goods_value['quantity']/$goods_value['unit_convert_rate'])); //单位为吨
                $return['contract_amount'] = $goods_value['contract_amount_cny'];
            }
        }
        $return['not_delivery_quantity'] = $contract_goods_quantity;

        if(!empty($stockOutlist)){
            foreach($stockOutlist as $key=>$value){
                $return['delivery_quantity'] += self::getQuantityTon_sell($value);//已提货数量
                //已交货货值
                if($value['contract_currency']==ConstantMap::CURRENCY_RMB)
                    $return['delivery_amount'] += self::getQuantityTon_sell($value)*($value['contract_price']*$value['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                else //若是美元，需要转换为人民币
                    $return['delivery_amount'] += self::getQuantityTon_sell($value)*($value['contract_price']*$value['exchange_rate']*$value['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
            }

        }

        $return['not_delivery_quantity'] = $contract_goods_quantity - $return['delivery_quantity'];
        $return['not_delivery_amount'] = $return['contract_amount'] - $return['delivery_amount'];
        if($value['contract_status']>=Contract::STATUS_SETTLED){//合同已结算
            //$return['delivery_quantity'] = self::getContractQuantity($value['contract_id']);
            $return['not_delivery_quantity'] = 0;
            $return['not_delivery_amount'] = 0;
        }
        $return['stock_out_list'] = $stockOutlist;
        return $return;
    }

    /**
     * @name:getQuantityTon_sell
     * @desc: 返回计量单位为吨的数值
     * @param:* @param $value   contract_unit 合同单位 ，stock_in_unit 入库单位，stock_in_unit_sub 入库子单位，unit_rate 入库单位和子单位的转化比
     *                          unit_convert_rate 销售合同的单位换算比，buy_unit_convert_rate 采购合同的单位换算比， quantity出库数量
     * @throw:
     * @return:float
     */

    protected static function getQuantityTon_sell($value){

       /* if($value['contract_unit']==ConstantMap::UNIT_TON){
            return $value['quantity'];
        }else{
            if(in_array($value['contract_unit'],array($value['stock_in_unit'],$value['stock_in_unit_sub']))){
                    if(in_array(ConstantMap::UNIT_TON,array($value['stock_in_unit'],$value['stock_in_unit_sub']))){//若入库单位里有吨，通过入库单换算比计算
                        if($value['stock_in_unit']==ConstantMap::UNIT_TON)
                            return $value['quantity']*$value['unit_rate'];
                        elseif($value['stock_in_unit_sub']==ConstantMap::UNIT_TON)
                            return $value['quantity']/$value['unit_rate'];
                    }else{//若入库单位里没有吨，通过入库单换算比和合同换算比一起计算

                        if($value['stock_in_unit']==$value['contract_unit'])
                            return $value['quantity']/($value['unit_rate']*$value['buy_unit_convert_rate']);
                        elseif($value['stock_in_unit_sub']==$value['contract_unit'])
                            return $value['buy_unit_convert_rate']==0?$value['quantity']:(($value['quantity']*$value['unit_rate'])/$value['buy_unit_convert_rate']);
                    }
            }else{//若出单位和入库单位不一致
                return $value['unit_convert_rate']==0?$value['quantity']:($value['quantity']/$value['unit_convert_rate']);
            }
        }*/

        return $value['unit_convert_rate']==0?$value['quantity']:($value['quantity']/$value['unit_convert_rate']);

    }
    /**
     * @desc 统计单个合同下的已收款金额:已认领金额之和 - 已实付金额之和
     */
    protected static function getReceiveConfirmArr($contract_id){
        $pay_amount = 0;
        $receive_amount = 0;
        $subject_list = ContractOverdue::SUBJECT_TYPE_ONE.",".ContractOverdue::SUBJECT_TYPE_SEVEN.",".ContractOverdue::SUBJECT_TYPE_EIGHT;
        if(!empty($contract_id)) {
            $contractInfo = Utility::query("select * from t_contract where contract_id = " . $contract_id);
            //付款
            $pay_amount = self::getContractPaymentNew($contract_id,$subject_list);
            //收款认款金额
            $receive_sql = "
                select
                       a.* from t_receive_confirm a

                where a.contract_id = " . $contract_id ." and a.subject in (".$subject_list.") and a.status >= ".ReceiveConfirm::STATUS_SUBMITED."
               ";
            $receiveList = Utility::query($receive_sql);
            if(!empty($receiveList)){
                foreach ($receiveList as $key=>$value) {
                    $receive_amount += $value['amount_cny'];
                }
            }

            return $receive_amount-$pay_amount;
        }
        return 0;
    }
    /**
     * @desc 统计单个合同下销项票情况
     */
    protected static function getInvoiceArr_sell($contract_id,$stock_out_list){
        $return =array(
            'invoice_quantity'=>0,//已开票数量
            'not_invoice_quantity_delivery'=>0,//未开票数量，按交货
            'not_invoice_amount_delivery'=>0,//未开票金额，按交货
            'not_invoice_quantity_contract'=>0,//未开票数量，按合同
            'not_invoice_amount_contract'=>0,//未开票金额，按合同
            'settle_quantity'=>0,
            'settle_amount'=>0
        );
        $sql="
                select
                         a.*,c.unit_convert_rate
                from
                         t_invoice_application_detail a
                         left join t_invoice_application b on a.apply_id = b.apply_id
                         left join t_contract_goods c on c.contract_id = b.contract_id and c.goods_id = a.goods_id
                where b.contract_id = '{$contract_id}' and b.status=".InvoiceApplication::STATUS_PASS." and b.type_sub =".InvoiceApplication::SUB_TYPE_GOODS." and b.type =".InvoiceApplication::TYPE_SELL."
             ";
        $invoiceList=Utility::query($sql);
        $contract_settlement_list = Utility::query("select a.* from t_contract_settlement_goods a left join t_contract b on a.contract_id=b.contract_id where a.contract_id=".$contract_id." and b.status=".Contract::STATUS_SETTLED);
        $contract_goods = Utility::query("select a.contract_id,a.quantity,a.goods_id,a.price,a.unit_convert_rate,b.currency contract_currency,b.exchange_rate,b.status contract_status from t_contract_goods a left join t_contract b on a.contract_id = b.contract_id where a.contract_id=".$contract_id);
        if(!empty($contract_goods)){
            foreach($contract_goods as $k=>$v){//循环商品
                $v_invoice_quantity = 0;//每个商品的收票数量（吨）
                $v_invoice_amount = 0;//每个商品的收票金额（元）
                $v_delivery_quantity = 0; //每个商品的提货数量（吨）
                $v_not_invoice_quantity_delivery = 0; //每个商品的未开票数量（吨），按交货
                $v_not_invoice_amount_delivery = 0; //每个商品的未开票金额（人民币），按交货

                $v_not_invoice_quantity_contract = 0; //每个商品的未开票数量（吨），按合同
                $v_not_invoice_amount_contract = 0; //每个商品的未开票金额（吨），按合同

                if(!empty($invoiceList)){
                    foreach($invoiceList as $key=>$value){
                        if($v['goods_id']==$value['goods_id']) {
                            $v_invoice_quantity += $value['quantity'] / $value['unit_convert_rate'];//已开票数量
                            $v_invoice_amount += $value['amount'];
                        }

                    }
                }

                if(!empty($stock_out_list)){
                    foreach($stock_out_list as $stock_key=>$stock_value){
                        if($v['goods_id']==$stock_value['goods_id']){
                            $v_delivery_quantity += self::getQuantityTon_sell($stock_value);
                        }
                    }
                }
                $v_not_invoice_quantity_delivery = $v_delivery_quantity-$v_invoice_quantity;
                if($v['contract_currency']==ConstantMap::CURRENCY_RMB)
                    $v_not_invoice_amount_delivery = $v_not_invoice_quantity_delivery*($v['price']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                else //若是美元，需要转换为人民币
                    $v_not_invoice_amount_delivery = $v_not_invoice_quantity_delivery*($v['price']*$v['exchange_rate']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价

                //未开票数量，按合同
                $v['unit_convert_rate'] = $v['unit_convert_rate']=='0.0000'?1:$v['unit_convert_rate'];
                $v_not_invoice_quantity_contract = ($v['quantity']/$v['unit_convert_rate'])-$v_invoice_quantity; //合同数量 - 已开票数量
                if($v['contract_currency']==ConstantMap::CURRENCY_RMB)
                    $v_not_invoice_amount_contract = $v_not_invoice_quantity_contract*($v['price']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                else //若是美元，需要转换为人民币
                    $v_not_invoice_amount_contract = $v_not_invoice_quantity_contract*($v['price']*$v['exchange_rate']*$v['unit_convert_rate']); //$value['contract_price']*$value['unit_convert_rate'] 是换算单价
                //未开票数量，按合同——合同已结算
                $settle_quantity=0;
                $settle_amount=0;
                if($v['contract_status']>=Contract::STATUS_SETTLED) {//合同已结算
                    if(!empty($contract_settlement_list)){
                        foreach($contract_settlement_list as $settle_key=>$settle_value){

                            if($v['goods_id']==$settle_value['goods_id']) {
                                $settle_quantity += $settle_value['quantity']/$v['unit_convert_rate'];//吨
                                $settle_amount += $settle_value['amount_cny'];
                            }
                        }
                    }
                    $v_not_invoice_quantity_contract = $settle_quantity-$v_invoice_quantity; //结算数量 - 已开票数量
                    $v_not_invoice_amount_contract = $settle_amount-$v_invoice_amount;

                }

                $return['invoice_quantity'] += $v_invoice_quantity;//全部商品的开票数量之和
                $return['not_invoice_quantity_delivery'] += $v_not_invoice_quantity_delivery;//全部商品的未开票数量之和，按交货
                $return['not_invoice_amount_delivery'] += $v_not_invoice_amount_delivery;//全部商品的未开票金额之和，按交货
                $return['not_invoice_quantity_contract'] += $v_not_invoice_quantity_contract;//全部商品的未开票数量之和，按合同
                $return['not_invoice_amount_contract'] += $v_not_invoice_amount_contract;//全部商品的未开票金额之和，按合同
                $return['settle_quantity'] += $settle_quantity;//全部商品 结算数量
                $return['settle_amount'] += $settle_amount; //全部商品 金额
            }


        }

        $return['invoice_quantity'] = round ($return['invoice_quantity'],4);
        $return['not_invoice_quantity_delivery'] = round ($return['not_invoice_quantity_delivery'],4);
        $return['not_invoice_quantity_contract'] = round ($return['not_invoice_quantity_contract'],4);

        return $return;
    }
}
