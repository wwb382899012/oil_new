<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/8 10:39
 * Describe：
 *      公司主体信息
 */
class Corporation extends BaseCacheActiveRecord
{

    /**
     * 获取缓存key
     * @param string $key
     * @return string
     */
    public static function getCacheKey($key="")
    {
        return static::$cacheKeyPrefix.__CLASS__.$key;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_corporation";
    }

    public function relations()
    {
        return array(
            "account" => array(self::HAS_MANY, "Account", "corporation_id"),//项目信息
        );
    }

    /**
     * 获取可选的公司主体信息
     * @param int $corporation_id
     * @return array
     */
    public static function getActiveCorporations($corporation_id = 0)
    {
        $res=self::getCache("cor_".$corporation_id);
        if(!empty($res))
            return json_decode($res,true);
        $condition="status=1";
        if(!empty($corporation_id))
            $condition.=" and corporation_id=".$corporation_id."";
        $data=Corporation::model()->findAllToArray(array(
            "select"=>"corporation_id,name,code",
            "condition"=>$condition,
            "order"=>"corporation_id desc"));
        self::setCache("cor_".$corporation_id,json_encode($data));
        return $data;

        /*$query = '';
        if(Utility::checkQueryId($corporation_id)) {
            $query .= ' and corporation_id = '.$corporation_id;
        }
        $sql="select corporation_id,name,code from t_corporation where status=1 " . $query . " order by corporation_id desc";
        return Utility::query($sql);*/
    }

    /**
     * 获取公司信息
     * @param $corporationId
     * @return array|mixed
     */
    public static function getCorporation($corporationId)
    {
        $res=self::getCache("cor_".$corporationId);
        if(!empty($res)) {
            $data = json_decode($res,true);
            return $data[0];
        }

        $data=Corporation::model()->findAllToArray('corporation_id = :corporationId', array('corporationId'=>$corporationId));
        self::setCache("cor_".$corporationId,json_encode($data));
        return $data[0];
    }


    /**
     * 删除
     * @param $id
     * @return int|string
     */
    public static function del($id)
    {
        if(empty($id))
            return "id不能为空！";
        if(!Utility::isIntString($id))
            return "非法Id";


        $rows=Corporation::model()->deleteByPk($id);
        if($rows==1)
        {
            self::clearCache();
            return 1;
        }
        else
            return "操作失败！";
    }



    /**
     * 获取公司编码
     * @param $corporationId
     * @return mixed|string
     */
    public static function getCorporationCode($corporationId)
    {
        $data=self::getCorporation($corporationId);
        if(!empty($data))
            return trim($data["code"]);
        else
            return "";
    }

    /**
     * 获取公司主体名
     * @param $corporationId
     * @return mixed|string
     */
    public static function getCorporationName($corporationId)
    {
        $data=self::getCorporation($corporationId);
        if(!empty($data))
            return trim($data["name"]);
        else
            return "";
    }


    /**
    *   获取公司对应账号信息
    */
    public static function getActiveCorporationAccounts($corporation_id = 0)
    {
        $res=self::getCache("cor_account_".$corporation_id);
        if(!empty($res))
            return json_decode($res,true);
        $condition="t.status=1";
        if(!empty($corporation_id))
            $condition.=" and t.corporation_id=".$corporation_id."";
        $data=Corporation::model()->with("account")->findAllToArray(array(
            "select"=>"t.corporation_id,name,code,account.account_id,account.bank_name,account.account_no",
            "condition"=>$condition,
            "order"=>"t.corporation_id desc"));
        self::setCache("cor_account_".$corporation_id,json_encode($data));
        return $data;
    }
}