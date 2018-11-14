<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/10/22
 * Time: 10:27
 * Describe：
 */
class SystemModule extends BaseActiveRecord
{
    /**
     * redis系统模块缓存标识
     */
    const RedisSystemModuleKey="NewOilSystemModule";

    /**
     * redis变量的前缀名
     */
    const RedisPrefix="New_Oil_";

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_system_module';
    }

    /**
     * 缓存是否有效
     * @return bool
     */
    public static function cacheIsValid()
    {
        $redis = Mod::app()->redis;
        if($redis->hExists(SystemModule::RedisSystemModuleKey,"status"))
            return true;
        else
            return false;
    }

    /**
     * 设置Redis缓存
     * @param $fieldName
     * @param $value
     */
    public static function setCache($fieldName,$value)
    {
        $redis = Mod::app()->redis;
        $redis->hSet(SystemModule::RedisSystemModuleKey,$fieldName,$value);
    }

    /**
     * 获取缓存数据
     * @param $fieldName
     * @param int $dataType 0:string,1:复杂数据，已经调用json_decode
     * @return mixed|null
     */
    public static function getCache($fieldName,$dataType=1)
    {
        $redis = Mod::app()->redis;
        if($redis->hExists(SystemModule::RedisSystemModuleKey,$fieldName))
        {
            $s=$redis->hGet(SystemModule::RedisSystemModuleKey, $fieldName);
            if($dataType==1)
                return json_decode($s,true);
            else
                return $s;
        }
        else
            return null;
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        $redis = Mod::app()->redis;
        $redis->delete(SystemModule::RedisSystemModuleKey);
        $redis->delete(SystemUser::RedisSystemRightKey);
    }

    /**
     * 保存，成功返回1，其他返回错误信息
     * @return int|string
     * @throws Exception
     */
    public function save($runValidation=true,$attributes=null)
    {
        $this->code= strtolower(trim($this->code));
        $isAdd=false;
        if($this->isNewRecord)
        {
            $isAdd=true;
        }
        else
        {
            $oldObj=SystemModule::model()->findByPk($this->id);
        }
        $model=SystemModule::model()->find("code=:code",array(":code"=>$this->code));
        if(!empty($model->id) && $model->id!=$this->id)
        {
            $this->errorMessage="当前权限码的模块已经存在，请重新填写！";
            return "当前权限码的模块已经存在，请重新填写！";
        }

        /*$sql="select * from t_system_module where code='".$this->code."' and id<>'".$this->id."'";
        if(Utility::queryIsEmpty($sql)==0)
            return "当前权限码的模块已经存在，请重新填写！";*/

        if($this->parent_id!=$this->getOldAttribute("parent_id"))
            $this->parent_ids=$this->getParentIds($this->parent_id);

        $trans=$this->dbConnection->getCurrentTransaction();
        if(!empty($trans))
            $isInDbTrans=true;
        else
            $isInDbTrans=false;

        if(!$isInDbTrans)
        {
            $trans = $this->dbConnection->beginTransaction();
        }

        try {

            $res=parent::save();
            if(!$isAdd && $this->parent_id!=$this->getOldAttribute("parent_id"))
            {
                $this->updateParentIds($this->getOldAttribute("parent_ids"),$this->parent_ids);
            }

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            $this->clearCache();
            return 1;
            //return $res;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return $e->getMessage();
            }
            else
                throw $e;
        }


        /*$db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {

            parent::save();
            $trans->rollback();
            return "OK";
            if(!$isAdd)
            {
                $this->updateParentIds($oldObj->parent_ids,$this->parent_ids);
            }

            $trans->commit();
            $this->clearCache();
            return 1;
        } catch (Exception $e) {
            try {
                $trans->rollback();
            }catch(Exception $ee){}
            return $e->getMessage();
        }*/
    }

    /**
     * 删除成功返回1，否则返回错误信息或0
     * @return int|string
     */
    public static function del($id)
    {
        if(empty($id))
            return "id不能为空！";

        if(!Utility::isIntString($id))
            return "非法Id";

        $sql="select count(id) as n from t_system_module where parent_id=".$id." and system_id=".Utility::getSystemId()."";

        if(Utility::queryOneNumber($sql,"n")>0)
        {
            return "请删除当前模块的子模块！";
        }
        $sql="delete from t_system_module where id=".$id." and system_id=".Utility::getSystemId()." and id not in(select a.parent_id  from (select parent_id from t_system_module) a)";
        $res=Utility::execute($sql);
        if($res==1)
        {
            $obj=new SystemModule();
            $obj->clearCache();
            return 1;
        }
        else
            return "操作失败！";
    }

    /**
     * 获取父路径
     * @param $parent_id
     * @return string
     */
    public function getParentIds($parent_id)
    {
        if($parent_id<1)
            return "0,";
        else
        {
            $sql="select parent_ids from t_system_module where id=".$parent_id." and system_id=".Utility::getSystemId()."";
            $data=Utility::query($sql);
            if(!Utility::isEmpty($data))
            {
                return $data[0]["parent_ids"].$parent_id.",";
            }
            else
                return "0,";
        }
    }


    /**
     * 更新父路径
     * @param $oldParentIds
     * @param $parentIds
     */
    public function updateParentIds($oldParentIds,$parentIds)
    {
        if($oldParentIds!=$parentIds)
        $sql="update t_system_module set parent_ids=replace(parent_ids,'" . $oldParentIds . "', '" .$parentIds. "') where parent_ids like '%,".$this->id.",%' and system_id=".Utility::getSystemId()."";
        Utility::executeSql($sql);
    }

    /**
     * 获取所有的类别，指定父类别则获取所有子类别
     *
     * @param int $parentId
     * @return mixed
     */
    public static function getAllData($parentId=0)
    {
        $res=SystemModule::getCache("allData_".$parentId);
        if(!empty($res))
            return $res;
        if($parentId!=0)
        {
            $sql="select * from t_system_module where parent_ids like '%," . $parentId . ",%' and system_id=".Utility::getSystemId()."  order by parent_id asc,order_index asc,id asc";
        }
        else
            $sql="select * from t_system_module where system_id=".Utility::getSystemId()." order by parent_id asc,order_index asc,id asc";
        $data= Utility::query($sql);
        SystemModule::setCache("allData_".$parentId,$data);
        return $data;
    }

    /**
     * 获取所有启用的模块信息
     * @param int $parentId
     * @return mixed
     */
    public static function getAllActiveData($parentId=0)
    {
        $res=SystemModule::getCache("allActiveData_".$parentId);
        if(!empty($res))
            return $res;
        if($parentId!=0)
        {
            $sql="select * from t_system_module where status=1 and system_id=".Utility::getSystemId()." and parent_ids like '%," . $parentId . ",%'  order by parent_id asc,order_index asc,id asc";
        }
        else
            $sql="select * from t_system_module where status=1 and system_id=".Utility::getSystemId()." order by parent_id asc,order_index asc,id asc";
        $data= Utility::query($sql);
        SystemModule::setCache("allActiveData_".$parentId,$data);
        return $data;
    }


    /**
     * 获取树形数据表
     * @param int $parentId
     * @return array
     */
    public static function getTreeTable($parentId=0)
    {
        $data=SystemModule::getAllData($parentId);
        $d=SystemModule::getTreeTableItem($data,0);
        return $d;
    }

    /**
     * 获取树形结构的递归子方法
     * @param $data
     * @param $id
     * @return array
     */
    protected static function getTreeTableItem($data,$id)
    {
        $d=array();
        foreach($data as $v)
        {
            if($v["parent_id"]==$id)
            {
                $d[]=$v;
                $children=SystemModule::getTreeTableItem($data,$v["id"]);
                $d=array_merge($d,$children);
            }
        }
        return $d;
    }

    /**
     * 获取所有状态为启用的模块树形数据表
     * @return array
     */
    public static function getActiveTreeTable()
    {
        $data=SystemModule::getAllActiveData();
        $d=SystemModule::getTreeTableItem($data,0);
        return $d;
    }

    /**
     * 获取系统模块以id为key的字典数组
     * @return array
     */
    public static function getModuleDic()
    {
        $res=SystemModule::getCache("ModuleIdDic");
        if(!empty($res))
            return $res;

        $data=SystemModule::getAllData();
        $dic=array();
        foreach($data as $v)
        {
            $dic[$v["id"]]=$v;
        }
        SystemModule::setCache("ModuleIdDic",json_encode($dic));
        return $dic;
    }

    /**
     * 获取以模块编码为key的字典数组
     * @return array|mixed|null
     */
    public static function getModuleCodeDic()
    {
        $res=SystemModule::getCache("ModuleCodeDic");
        if(!empty($res))
            return $res;

        $data=SystemModule::getAllData();
        $dic=array();
        foreach($data as $v)
        {
            $dic[strtolower($v["code"])]=$v;
        }
        SystemModule::setCache("ModuleCodeDic",json_encode($dic));
        return $dic;
    }

    /**
     * 获取树形数组的模块信息
     * @param $data
     * @param $id
     * @return array
     */
    public static function getTreeNode($data,$id)
    {
        $d=array();
        foreach($data as $v)
        {
            if($v["parent_id"]==$id && $v["is_menu"]==1)
            {
                $v["children"]=SystemModule::getTreeNode($data,$v["id"]);
                $d[]=$v;
            }
        }
        return $d;
    }

}