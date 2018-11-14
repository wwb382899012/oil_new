<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/11 15:29
 * Describe：
 */

class StatService
{

    public static $config=array(
        "1"=>array(
            "title"=>"近七天收付款信息",
            "fn"=>"getPaymentStat",
        ),
        "2"=>array(
            "title"=>"近七天采销合同金额信息",
            "fn"=>"getNewContractAmount",
        ),
    );

    public static function getConfig($id)
    {
        return self::$config[$id];
    }

    /**
     * 近七天收付款信息
     * @return array
     */
    public static function getPaymentStat()
    {
        $stat=array();
        $receive=array();
        for ($i=6;$i>=0;$i--)
        {
            $d=Utility::getDate("-".$i." days");
            $stat[$d]=0;
            $receive[$d]=0;
        }

        $date=Utility::getDate("-6 days");

        $sql="select p.pay_date,sum(p.amount)/100 amount
                from t_payment p 
                    left join t_pay_application b on p.apply_id=b.apply_id
                where p.status>=2 and p.pay_date>='".$date."'
                group by p.pay_date";

        $data=Utility::query($sql);
        foreach ($data as $v)
        {
            $stat[$v["pay_date"]]=floatval($v["amount"]);
        }

        $sql="select b.receive_date,sum(a.amount)/100  amount
                from t_receive_confirm a
                left join t_bank_flow b on a.flow_id=b.flow_id
                where a.status>=1 and b.receive_date>='".$date."'
                group by b.receive_date";

        $data=Utility::query($sql);
        foreach ($data as $v)
        {
            $receive[$v["receive_date"]]=floatval($v["amount"]);
        }

        $data=array();
        $data["x"]=array_keys($stat);
        $data["series"][]=array(
            "data"=>array_values($stat),
            "color"=>"#FF0000",
            "name"=>"付款金额",
        );
        $data["series"][]=array(
            "data"=>array_values($receive),
            "color"=>"#7CB5EC",
            "name"=>"收款金额",
        );

        $data["title"]="近七天收付款信息";
        return $data;

    }

    /**
     * 近七天采销合同金额信息
     * @return array
     */
    public static function getNewContractAmount()
    {
        $stat=array();
        $keys=array();
        for ($i=6;$i>=0;$i--)
        {
            $d=Utility::getDate("-".$i." days");
            $keys[]=$d;
            $stat[1][$d]=0;
            $stat[2][$d]=0;
        }

        $date=Utility::getDate("-6 days");
        $sql="select type,date(create_time) contract_date ,sum(amount_cny)/100 amount
                from t_contract 
                where status>=".Contract::STATUS_BUSINESS_CHECKED." and create_time>='".$date."'
                group by date(create_time),type";

        $data=Utility::query($sql);
        foreach ($data as $v)
        {
            $stat[$v["type"]][$v["contract_date"]]=floatval($v["amount"]);
        }

        $data=array();
        $data["x"]=$keys;
        $data["series"][]=array(
            "data"=>array_values($stat[1]),
            "color"=>"#FF0000",
            "name"=>"采购合同金额",
        );
        $data["series"][]=array(
            "data"=>array_values($stat[2]),
            "color"=>"#7CB5EC",
            "name"=>"销售合同金额",
        );

        $data["title"]="近七天采销合同金额信息";
        return $data;
    }
}