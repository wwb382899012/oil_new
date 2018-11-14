<?php
/**
 * Created by youyi000.
 * DateTime: 2018/7/26 16:16
 * Describe：
 *  后续可以从数据中获取
 */

namespace ddd\Common\Domain\Currency;


use ddd\infrastructure\error\ZException;

class CurrencyService
{

    /**
     * 人民币
     */
    const CNY=1;
    /**
     * 美元
     */
    const USD=2;

    protected static $configs=[
        1=>["name"=>"人民币","symbol"=>"￥"],
        2=>["name"=>"美元","symbol"=>"$"],
    ];

    private static $instances=[];

    /**
     * @param $currencyId
     * @return Currency
     * @throws \Exception
     */
    public static function create($currencyId)
    {
        if(key_exists($currencyId,self::$instances))
            return self::$instances[$currencyId];

        //if(!key_exists($currencyId,self::$configs))
        if(!key_exists($currencyId,\Map::$v["currency"]))
            throw new ZException("指定币种不存在");
        $currencyConfig=\Map::$v["currency"];
        return self::$instances[$currencyId] = new Currency($currencyId,$currencyConfig[$currencyId]["name"],$currencyConfig[$currencyId]["ico"]);
    }

    /**
     * @return Currency
     */
    public static function createCNY()
    {
        return CurrencyService::create(CurrencyService::CNY);
        //return new Currency(1,"人民币","￥");
    }
}