<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/23 10:22
 * Describe：
 */

class ProfitService
{

    public static $goodsSubjectIds="1,7,8";//货款类财务科目（货款、履约保证金、预付款）

    public static $stamp_tax_rate=0.0003;//印花税率

    public static $surtax_rate=0.12;//附加税率

    public static $tax_rate=0.17;//增值税率

    public static $factoring_rate = 0.5; //保理服务费率

    public static $factoring_huoer_rate = 0.275; //保理服务费率（霍尔果斯）

    /**
     * 计算所有的未完成的有效的项目的收支信息
     * @param $date
     */
    public static function computeProfit($date)
    {
        //计算项目下的费用
        $projects=Project::model()->findAll("status<".Project::STATUS_DONE." and status>=".Project::STATUS_SUBMIT);
        foreach ($projects as $p)
        {
            self::computeProjectProfit($p->project_id,$date,$p);

            self::computeProjectCost($p->project_id,$date,$p);
        }

        //计算只挂在交易主体下的一些费用
        $corps=Corporation::model()->findAll();
        foreach ($corps as $c)
        {
            self::computeCorpProfit($c->corporation_id,$date);

            self::computeCorpCost($c->corporation_id,$date);
        }
    }



    /**
     * 计算项目的收付款信息
     * @param $projectId
     * @param $date
     * @param null $project
     * @return bool
     */
    public static function computeProjectProfit($projectId,$date,$project=null)
    {
        if(empty($project) && empty($projectId))
            return false;
        if(empty($project))
            $project=Project::model()->findByPk($projectId);
        if(empty($project))
            return false;

        $statDate=strtotime($date);
        $year=date("Y",$statDate);
        $month=date("m",$statDate);
        $profit=ProjectProfit::model()->find("project_id=".$projectId." and stat_year=".$year." and stat_month=".$month."");
        if(empty($profit))
        {
            $profit=new ProjectProfit();
            $profit->project_id=$project->project_id;
            $profit->corporation_id=$project->corporation_id;
            $profit->stat_year=$year;
            $profit->stat_month=$month;
        }

        //计算发票金额
        $sql="select type,ifnull(sum(amount), 0) as amount from t_invoice_application 
              where 
              project_id=".$projectId." 
              and invoice_date<='".$date."' 
              and status>=".InvoiceApplication::STATUS_PASS."
              and type_sub=".InvoiceApplication::SUB_TYPE_GOODS."
              group by type";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            foreach ($data as $v)
            {
                if($v["type"]==InvoiceApplication::TYPE_BUY)
                    $profit->buy_amount_invoice=$v["amount"];
                else if($v["type"]==InvoiceApplication::TYPE_SELL)
                    $profit->sell_amount_invoice=$v["amount"];
            }
        }


        //计算采购结算金额
        $sql="select ifnull(sum(a.amount_cny), 0) as amount from t_stock_batch_settlement a,t_stock_in_batch b
              where a.batch_id=b.batch_id and a.project_id=".$projectId." and date(b.status_time)<='".$date."' and b.status>=".StockNotice::STATUS_SETTLED."
              ";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            $profit->buy_amount_settle=$data[0]["amount"];

        //计算销售结算金额
        //TODO::加入发货单结算审核后，发货单状态需调整
        $sql="select ifnull(sum(a.amount_cny),0) as amount from t_delivery_order b,t_delivery_settlement a
              where a.order_id=b.order_id and a.project_id=".$projectId." and date(b.status_time)<='".$date."' and b.status>=".DeliveryOrder::STATUS_SETTLE_SUBMIT."
              ";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            $profit->sell_amount_settle=$data[0]["amount"];

        //计算采购实付金额
        $profit->buy_amount_paid=0;
        //正常付款的
        $sql="select ifnull(sum(b.amount),0) amount from t_pay_application a,t_payment b 
              where a.apply_id=b.apply_id and a.project_id=".$projectId." 
                    and b.pay_date<='".$date."' 
                    and a.category=".PayApplication::CATEGORY_NORMAL."
                    and a.subject_id in(".self::$goodsSubjectIds.")
                    and b.status>=".Payment::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            $profit->buy_amount_paid+=$data[0]["amount"];
        //后认领的
        $sql="select ifnull(sum(b.amount),0) amount from t_pay_application a,t_pay_claim b 
              where a.apply_id=b.apply_id 
                    and b.project_id=".$projectId." 
                    and a.category=".PayApplication::CATEGORY_CLAIMING."
                    and a.subject_id in(".self::$goodsSubjectIds.")
                    and date(b.status_time)<='".$date."' 
                    and b.status>=".PayClaim::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            $profit->buy_amount_paid+=$data[0]["amount"];


        //计算销售回款金额
        $sql="select ifnull(sum(a.amount),0) amount from t_receive_confirm a
              where a.project_id=".$projectId." 
                    and a.receive_date<='".$date."' 
                    and a.subject in(".self::$goodsSubjectIds.")
                    and a.status>=".ReceiveConfirm::STATUS_SUBMITED."";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            $profit->sell_amount_paid=$data[0]["amount"];

        if($profit->sell_amount_paid < $profit->sell_amount_settle
            || $profit->sell_amount_invoice < $profit->sell_amount_settle
            || $profit->buy_amount_invoice < $profit->buy_amount_settle)
        {
            $profit->type=ProjectProfit::TYPE_SETTLED;
        }
        else
        {
            $profit->type=self::getProjectSettleStatus($projectId,$date);
        }


        $res=$profit->save();
        if(!$res)
        {
            Mod::log("ProjectProfit save error, projectId is ".$projectId." and Date is ".$date,"error");
        }
        return $res;

    }

    /**
     * 判断项目的出库单对应入库单的结算状态是否满足可分配
     * @param $projectId
     * @param $date
     * @return int
     */
    public static function getProjectSettleStatus($projectId,$date)
    {
        $type=ProjectProfit::TYPE_CONFIRM;

        $sql="select a.stock_id,c.status 
              from t_stock a
              left join t_stock_in b on a.stock_in_id=b.stock_in_id
              left join t_stock_in_batch c on b.batch_id=c.batch_id
              ,t_stock_out_detail oa
              left join t_delivery_order ob on oa.order_id=ob.order_id
              where 
                a.stock_id=oa.stock_id 
                and oa.project_id=".$projectId."
                and date(ob.status_time)<='".$date."' 
                and ob.status>=".DeliveryOrder::STATUS_SETTLE_PASS."
                and (c.status<".StockNotice::STATUS_SETTLED." 
                    or (c.status>=".StockNotice::STATUS_SETTLED." and date(c.status_time)>'".$date."')
                    )
                ";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            $type=ProjectProfit::TYPE_SETTLED;
        }

        return $type;

    }

    /**
     * 计算项目的非货款类的成本费用
     * @param $projectId
     * @param $date
     * @param null $project
     * @return bool
     */
    public static function computeProjectCost($projectId,$date,$project=null)
    {
        if(empty($project) && empty($projectId))
            return false;
        if(empty($project))
            $project=Project::model()->findByPk($projectId);
        if(empty($project))
            return false;

        $statDate=strtotime($date);
        $year=date("Y",$statDate);
        $month=date("m",$statDate);
        $cost=ProjectCost::model()->find("project_id=".$projectId." and stat_year=".$year." and stat_month=".$month."");
        if(empty($cost))
        {
            $cost=new ProjectCost();
            $cost->project_id=$project->project_id;
            $cost->corporation_id=$project->corporation_id;
            $cost->stat_year=$year;
            $cost->stat_month=$month;
        }

        //计算保理相关费用
        $factorFees = ProjectService::computeProjectFactorRelatedFees($projectId, date('Y-m-d',strtotime($date. ' +1 month -1 day')));
        $cost->factoring_interest = $factorFees['factoring_interest'];
        $cost->factoring_fee = $factorFees['factoring_fee'];
        $cost->factoring_fee2 = $factorFees['factoring_fee2'];

        //计算增值税
        $profit=ProjectProfit::model()->find("project_id=".$projectId." and stat_year=".$year." and stat_month=".$month."");
        $buy=$profit->buy_amount_invoice/(1+self::$tax_rate)*self::$tax_rate;
        $sell=$profit->sell_amount_invoice/(1+self::$tax_rate)*self::$tax_rate;
        $cost->amount_tax=$sell-$buy;

        //计算附加税
        $cost->amount_surtax=$cost->amount_tax*self::$surtax_rate;

        //计算印花税
        $sql="select ifnull(sum(a.amount_cny),0) as amount 
              from t_contract_goods a,t_contract c
              where 
                a.contract_id=c.contract_id and c.project_id=".$projectId." 
              and c.status>=".Contract::STATUS_BUSINESS_CHECKED."
              ";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            $cost->amount_stamp=$data[0]["amount"]*self::$stamp_tax_rate;
        }

        //计算各种其他费用
        $sql="select a.subject_id,ifnull(sum(b.amount),0) amount from t_pay_application a,t_payment b 
              where a.apply_id=b.apply_id and a.project_id=".$projectId." 
                    and b.pay_date<='".$date."' 
                    and a.subject_id not in(".self::$goodsSubjectIds.")
                    and a.category=".PayApplication::CATEGORY_NORMAL."
                    and b.status>=".Payment::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."
              group by a.subject_id";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            foreach ($data as $v)
            {
                if($v["subject_id"]==5)
                    $cost->amount_store=$v["amount"];
                else if($v["subject_id"]==4)
                    $cost->amount_traffic=$v["amount"];
                else if($v["subject_id"]==9)
                    $cost->amount_other=$v["amount"];
            }
        }

        //后认领的费用
        $sql="select a.subject_id,ifnull(sum(b.amount),0) amount from t_pay_application a,t_pay_claim b 
              where a.apply_id=b.apply_id 
                    and b.project_id=".$projectId." 
                    and a.category=".PayApplication::CATEGORY_CLAIMING."
                    and a.subject_id not in(".self::$goodsSubjectIds.")
                    and date(b.status_time)<='".$date."' 
                    and b.status>=".PayClaim::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."
                    group by a.subject_id";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            foreach ($data as $v)
            {
                if($v["subject_id"]==5)
                    $cost->amount_store+=$v["amount"];
                else if($v["subject_id"]==4)
                    $cost->amount_traffic+=$v["amount"];
                else if($v["subject_id"]==9)
                    $cost->amount_other+=$v["amount"];
            }
        }


        $res=$cost->save();
        if(!$res)
        {
            Mod::log("ProjectCost save error, projectId is ".$projectId.", and date is ".$date,"error");
        }
        return $res;


    }


    /**
     * 计算交易主体下的一些费用
     * @param $corpId
     * @param $date
     * @return bool
     */
    public static function computeCorpProfit($corpId,$date)
    {
        if(empty($corpId))
            return false;

        $statDate=strtotime($date);
        $year=date("Y",$statDate);
        $month=date("m",$statDate);
        $profit=ProjectProfit::model()->find("corporation_id=".$corpId." and project_id=0 and stat_year=".$year." and stat_month=".$month."");
        if(empty($profit))
        {
            $profit=new ProjectProfit();
            $profit->project_id=0;
            $profit->corporation_id=$corpId;
            $profit->stat_year=$year;
            $profit->stat_month=$month;
        }


        //计算后认领未认领的付款
        $sql="select ifnull(sum(b.amount),0) amount from t_pay_application a,t_payment b 
              where a.apply_id=b.apply_id 
                    and a.corporation_id=".$corpId." 
                    and b.pay_date<='".$date."' 
                    and a.category=".PayApplication::CATEGORY_CLAIMING."
                    and a.subject_id in(".self::$goodsSubjectIds.")
                    and b.status>=".Payment::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."";
        $data=Utility::query($sql);
        $amount=0;
        if(Utility::isNotEmpty($data))
            $amount=$data[0]["amount"];

        $sql="select ifnull(sum(b.amount),0) amount from t_pay_application a,t_pay_claim b 
              where a.apply_id=b.apply_id 
                    and a.corporation_id=".$corpId."
                    and a.category=".PayApplication::CATEGORY_CLAIMING."
                    and a.subject_id in(".self::$goodsSubjectIds.")
                    and date(b.status_time)<='".$date."' 
                    and b.status>=".PayClaim::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            $amount=$amount-$data[0]["amount"];

        $profit->buy_amount_paid=$amount;

        $res=$profit->save();
        if(!$res)
        {
            Mod::log("ProjectProfit save error, corpId is ".$corpId." and Date is ".$date,"error");
        }
        return $res;

    }

    /**
     * 计算交易主体下的成本费用
     * @param $corpId
     * @param $date
     * @return bool
     */
    public static function computeCorpCost($corpId,$date)
    {
        if(empty($corpId))
            return false;

        $statDate=strtotime($date);
        $year=date("Y",$statDate);
        $month=date("m",$statDate);
        $cost=ProjectCost::model()->find("corporation_id=".$corpId." and project_id=0 and stat_year=".$year." and stat_month=".$month."");
        if(empty($cost))
        {
            $cost=new ProjectCost();
            $cost->project_id=0;
            $cost->corporation_id=$corpId;
            $cost->stat_year=$year;
            $cost->stat_month=$month;
        }

        //计算各种其他费用
        $sql="select a.subject_id,ifnull(sum(b.amount),0) amount from t_pay_application a,t_payment b 
              where a.apply_id=b.apply_id 
                  and a.project_id=0 
                  and a.corporation_id=".$corpId." 
                    and b.pay_date<='".$date."' 
                    and a.subject_id not in(".self::$goodsSubjectIds.")
                    and a.category=".PayApplication::CATEGORY_NORMAL."
                    and b.status>=".Payment::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."
              group by a.subject_id";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            foreach ($data as $v)
            {
                if($v["subject_id"]==5)
                    $cost->amount_store=$v["amount"];
                else if($v["subject_id"]==4)
                    $cost->amount_traffic=$v["amount"];
                else if($v["subject_id"]==9)
                    $cost->amount_other=$v["amount"];
            }
        }

        //加待认领的其他付款
        $sql="select a.subject_id,ifnull(sum(b.amount),0) amount from t_pay_application a,t_payment b 
              where a.apply_id=b.apply_id 
                  and a.project_id=0 
                  and a.corporation_id=".$corpId." 
                    and b.pay_date<='".$date."' 
                    and a.category=".PayApplication::CATEGORY_CLAIMING."
                    and a.subject_id not in(".self::$goodsSubjectIds.")
                    and a.category=".PayApplication::CATEGORY_NORMAL."
                    and b.status>=".Payment::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."
              group by a.subject_id";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            foreach ($data as $v)
            {
                if($v["subject_id"]==5)
                    $cost->amount_store+=$v["amount"];
                else if($v["subject_id"]==4)
                    $cost->amount_traffic+=$v["amount"];
                else if($v["subject_id"]==9)
                    $cost->amount_other+=$v["amount"];
            }
        }

        //减掉已认领
        $sql="select a.subject_id,ifnull(sum(b.amount),0) amount from t_pay_application a,t_pay_claim b 
              where a.apply_id=b.apply_id 
                     and a.corporation_id=".$corpId." 
                    and a.category=".PayApplication::CATEGORY_CLAIMING."
                    and a.subject_id not in(".self::$goodsSubjectIds.")
                    and date(b.status_time)<='".$date."' 
                    and b.status>=".PayClaim::STATUS_SUBMITED."
                    and a.status>=".PayApplication::STATUS_CHECKED."
                    group by a.subject_id";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            foreach ($data as $v)
            {
                if($v["subject_id"]==5)
                    $cost->amount_store-=$v["amount"];
                else if($v["subject_id"]==4)
                    $cost->amount_traffic-=$v["amount"];
                else if($v["subject_id"]==9)
                    $cost->amount_other-=$v["amount"];
            }
        }


        $res=$cost->save();
        if(!$res)
        {
            Mod::log("CorpCost save error, corpId is ".$corpId.", and date is ".$date,"error");
        }
        return $res;

    }

}