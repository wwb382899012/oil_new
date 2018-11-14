<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/24 11:19
 * Describe：
 */

class Subject extends BaseCacheActiveRecord
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
        return "t_finance_subject";
    }

    public static function getActiveSubjects()
    {
        $res=self::getCache("active");
        if(!empty($res))
            return json_decode($res,true);
        $condition="status=1";
        $data=Subject::model()->findAllToArray(array(
                                                   "select"=>"subject_id,name,code",
                                                   "condition"=>$condition,
                                                   "order"=>"subject_id asc"));
        self::setCache("active",json_encode($data));
        return $data;

    }


}