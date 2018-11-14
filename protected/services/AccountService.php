<?php

/**
 * Created by youyi000.
 * DateTime: 2016/6/28 11:55
 * Describe：
 */
class AccountService
{
    /**
     * 获取可选的账户信息
     * @return array
     */
    public static function getAccounts()
    {
        $sql="select account_id,account_no,account_name,bank_name,corporation_id from t_account where status=1 order by account_id desc";
        return Utility::query($sql);
    }

    /**
     * 根据账户ID获取对应开户银行信息
     * @return array
     */
    public static function getBankById($id)
    {
        $sql="select account_id,bank_name from t_account where status=1 and account_id={$id} order by account_id desc";
        return Utility::query($sql);
    }

    /**
     * 人民币中文大写与数字相互转换
     * @return array
     */
    public static function cny($ns)
    {
        $cnums          = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
        $cnyunits       = array("圆","角","分");
        $grees          = array("拾","佰","仟","万","拾","佰","仟","亿");
        list($ns1,$ns2) = explode(".",$ns,2);
        $ns2 = array_filter(array($ns2[1],$ns2[0]));
        $ret = array_merge($ns2,array(implode("", self::_cny_map_unit(str_split($ns1), $grees)), ""));
        $ret = implode("",array_reverse(self::_cny_map_unit($ret,$cnyunits)));
        return str_replace(array_keys($cnums), $cnums, $ret);
    }

    protected static function _cny_map_unit($list,$units)
    { 
        $ul = count($units);
        $xs = array();
        foreach (array_reverse($list) as $x)
        { 
            $l = count($xs);
            if($x!="0" || !($l%4))
            {
                $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
            }
            else
            {
                $n=is_numeric($xs[0][0]) ? $x : '';
            }
            array_unshift($xs, $n);
        } 
        return $xs;
    }
    
    

}