<?php

/**
 * Desc: 仓库信息
 * User: susiehuang
 * Date: 2017/8/26 0031
 * Time: 10:05
 */
class Storehouse extends BaseCacheActiveRecord
{

    const STATUS_BACK = -1;//已驳回
    const STATUS_NEW = 0;//未提交
    const STATUS_IN_APPROVAL = 10; //审批中
    const STATUS_PASS = 20; //审批通过

    /**
     * 获取缓存key
     * @param string $key
     * @return string
     */
    public static function getCacheKey($key="")
    {
        return static::$cacheKeyPrefix.__CLASS__.$key;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_storehouse";
    }

    /**
     * 获取所有可用的仓库信息
     * @return mixed
     */
    public static function getAllActiveStorehouse()
    {
        $res = self::getCache("activeData");
        if (!empty($res))
            return json_decode($res, true);
        $stores=Storehouse::model()->findAllToArray(array(
            'select' => 'store_id, name',
            'condition' => 'status = :status',
            'params' => array('status' => Storehouse::STATUS_PASS),
            "order"=>"store_id desc"));

        self::setCache("activeData", json_encode($stores));
        return $stores;

    }

    protected function afterSave()
    {
        parent::afterSave(); // TODO: Change the autogenerated stub
        self::clearCache();
        self::clearCache('activeData');
    }
}