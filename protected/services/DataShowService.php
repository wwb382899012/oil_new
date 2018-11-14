<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/22 9:52
 * Describe：
 */

class DataShowService
{

    public static $config=array(
        "1"=>array(
            "sql"=>"select * from (select start_date,cust_id ,sum(amount) amount from t_ast_bank_target 
                        where status=6
                        group by start_date,cust_id ) a {where} order by  start_date desc",
            "search_items"=>"",
            "columns"=>array('start_date:text:实际满标日期:width:150px;text-align:center;','cust_id:text:借款人Id:width:150px;text-align:center;','amount:amount:金额'),
            "is_export"=>0,
            "export_fields"=>"start_date as 实际满标日期,cust_id 借款人Id,amount/100 金额",
        ),
    );

    /**
     * 获取id对应的配置
     * @param $id
     * @return mixed
     */
    public static function getConfig($id)
    {
        return DataShowConfig::$config[$id];
    }

    /**
     * 获取查询的Sql
     * @param $id
     * @return mixed
     */
    public static function getSearchSql($id)
    {
        $config=static::getConfig($id);
        return $config["sql"];
    }

    public static function getExportSql($id)
    {
        $config=static::getConfig($id);
        $exportSql=$config["sql"];
        if(!empty($config["export_fields"]))
        {
            $pattern='/^select\s(.*?)\s+from\s+/is';
            $exportSql=preg_replace($pattern,'select '.$config["export_fields"].' from ',$exportSql);
            //echo $exportSql;
           /* preg_match($pattern, $config["sql"],$matches);
            if(is_array($matches) && count($matches)>0)
                $exportSql=str_replace($matches[1],$config["export_fields"] ,$exportSql);*/
        }
        return $exportSql;
    }


    public static function getColumns($id)
    {
        $config=static::getConfig($id);
        if(is_string($config["columns"]))
            return json_decode($config["columns"],true);
        else
            return $config["columns"];
    }

    public static function getSearchItems($id)
    {
        $config=static::getConfig($id);
        if(is_string($config["search_items"]))
            $items= json_decode($config["search_items"],true);
        else
            $items= $config["search_items"];

        $items[]=array("type"=>"hidden","key"=>"id","value"=>$id);
        return $items;
    }

    public static function checkIsCanExport($id)
    {
        $config=static::getConfig($id);
        if(!empty($config["is_export"]))
            return true;
        else
            return false;
    }

    public static function getDataProviderConfig($id)
    {
        $config=static::getConfig($id);
        if(isset($config["config"]))
            return $config["config"];
        else
            return array();
    }

    /**
     * 获取指定key的配置值，不存在返回null
     * @param $id
     * @param $key
     * @return null
     */
    public static function getConfigValue($id,$key)
    {
        $config=static::getConfig($id);
        if(isset($config[$key]))
            return $config[$key];
        else
            return null;
    }

    /**
     * @desc 获取map列表
     * @param $id
     * @return array | null
     */
    public static function getMapKeysConfig($id)
    {
        $config=static::getConfig($id);
        if(isset($config["map_keys"]))
            return $config["map_keys"];
        else
            return array();
    }

    /**
     * @desc 替换导出数据的map值
     * @param array $data
     * @param array $map
     * @return array
     */
    public static function getExportMapData($data, $map) {
        if(Utility::isNotEmpty($map) && Utility::isNotEmpty($data)) {
            foreach ($map as $key => $row) {
                foreach ($data as $index => $val) {
                    foreach ($val as $k => $v) {
                        if($row['name'] == $k) {
                            if(!empty($row["map_key"]))
                                $data[$index][$k] = Map::$v[$row["map_key"]][$v];
                            else
                                $data[$index][$k] = $row['list'][$v];
                        }
                    }
                }
            }
        }
        return $data;
    }
}