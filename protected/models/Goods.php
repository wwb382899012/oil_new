<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/21 10:39
 * Describe：
 */
class Goods extends BaseCacheActiveRecord
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
        return "t_goods";
    }

    public function relations()
    {
        return array(
            "parent" => array(self::BELONGS_TO, "Goods", "parent_id"),//项目发起信息
        );
    }

    /**
     * 清空缓存
     */
    public function beforeSave()
	{
		self::clearCache('allTree');
		self::clearCache('activeTree');
		return parent::beforeSave();
	}

    /**
     * 清空缓存
     */
	public function beforeDelete()
	{
		self::clearCache('allTree');
		self::clearCache('activeTree');
		return parent::beforeDelete();
	}

    /**
     * 获取所有启用的信息
     * @param int $parentId
     * @return mixed
     */
    public static function getAllActiveData($parentId=0)
    {
    	return Goods::getTreeData($parentId);
    }

    /**
     * 获取所有的信息
     * @param int $parentId
     * @return mixed
     */
    public static function getAllTreeData($parentId=0)
    {
    	return Goods::getTreeData($parentId, true);
    }

    private static function getTreeData($parentId = 0, $getAll = false) {
    	if($getAll) {
    		$cache_prefix = "allTreeData_";
        	$sql = "select t_goods.*, creater.name as creater_name, updater.name as updater_name from t_goods left join t_system_user creater on creater.user_id=t_goods.create_user_id left join t_system_user updater on updater.user_id=t_goods.update_user_id ";
    	} else {
    		$cache_prefix = "allActiveData_";
        	$sql = "select * from t_goods where status=1 ";
    	}
        // $res = self::getCache($cache_prefix . $parentId);
        if (!empty($res))
            return $res;
        if ($parentId != 0)
        {
            $sql .= " and  parent_ids like '%," . $parentId . ",%'";
        }

        $sql .= " order by parent_id asc,order_index asc";
        $data = Utility::query($sql);
        // self::setCache($cache_prefix . $parentId, $data);
        return $data;
    }


    /**
     * 获取树形结构的递归子方法
     * @param $data
     * @param $id
     * @param string $prefix 名称前缀，默认为空
     * @return array
     */
    protected static function getTreeTableItem($data,$id,$prefix="")
    {
        $d=array();
        foreach($data as $v)
        {
            if($v["parent_id"]==$id)
            {
                if(!empty($prefix))
                    $v["display_name"]=$v["name"];
                $d[]=$v;
                $children=self::getTreeTableItem($data,$v["goods_id"]);
                if(!empty($prefix))
                {
                    foreach ($children as $k=>$g)
                    {
                        $children[$k]["display_name"]=$prefix.$children[$k]["display_name"];
                    }
                }
                $d=array_merge($d,$children);
            }
        }
        return $d;
    }

    
    /**
     * 获取所有状态为启用的模块树形数据表
     * @param string $prefix 名称前缀，默认为空
     * @return array
     */
    public static function getActiveTreeTable($prefix="")
    {
        $res = self::getCache("activeTree");
        if (!empty($res))
            return json_decode($res, true);

        $data=self::getAllActiveData();
        $d=self::getTreeTableItem($data,0,$prefix);
        self::setCache("activeTree", json_encode($d));
        return $d;
    }

    protected static function getTreeItem($data,$id,$prefix="")
    {
        $d=array();
        foreach($data as $v)
        {
            if($v["parent_id"]==$id)
            {
                if(!empty($prefix))
                    $v["display_name"]=$v["name"];
                $children=self::getTreeItem($data,$v["goods_id"]);
                if(!empty($prefix))
                {
                    foreach ($children as $k=>$g)
                    {
                        $children[$k]["display_name"]=$prefix.$children[$k]["display_name"];
                    }
                }
                // $d=array_merge($d,$children);
                $v['children'] = $children;
                $d[]=$v;
            }
        }
        return $d;
    }

    public static function getActiveTree($prefix="", $getAll = false)
    {
    	$cacheKey = $getAll?"allTree":"activeTree";
		self::clearCache($cacheKey);
        $res = self::getCache($cacheKey);
        if (!empty($res))
            return json_decode($res, true);
        if($getAll) {
        	$data=self::getAllTreeData(0, $getAll);
        } else {
	        $data=self::getAllActiveData(0, $getAll);
        }
        $d=self::getTreeItem($data,0,$prefix);
        self::setCache($cacheKey, json_encode($d));
        return $d;
    }

}