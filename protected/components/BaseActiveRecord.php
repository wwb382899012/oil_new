<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/21 11:48
 * Describe：
 */
class BaseActiveRecord extends CActiveRecord
{

    public $errorMessage="";

    /**
     * 原始值，每次更新或find后更新
     * @var array
     */
    public $oldAttributes=array();

    /**
     * 每次保存时的差异值
     * @var array
     */
    public $diffAttributes=array();

    /**
     * 查询所有数据返回数组格式的数据
     * @param string $condition
     * @param array $params
     * @return array
     */
    public function findAllToArray($condition='',$params=array())
    {
        return array_map(function($record) {
            return $record->getAttributesWithRelations();
        }, $this->findAll($condition,$params));
    }

    /**
     * 获取包含relation的属性
     * @param bool $names
     * @param null $ignore
     * @return mixed
     */
    public function getAttributesWithRelations($names=true,$ignore=null)
    {
        $result = $this->getAttributes($names,$ignore);
        $relations= $this->relations();
        foreach ($relations as $k=>$r)
        {
            if($this->hasRelated($k))
            {
                if(is_array($this->$k))
                {
                    foreach ($this->$k as $kv)
                    {
                        $result[$k][]=$kv->getAttributesWithRelations($names,$ignore);
                    }
                }
                elseif($this->$k)
                {
                    $result[$k]=$this->$k->getAttributesWithRelations($names,$ignore);
                }
                else
                {
                    $result[$k]=array();
                }
            }
        }

        return $result;
    }

    /**
     * model数组转换成数组
     * @param $models
     * @param null $ignore
     * @return array
     */
    public function modelsToArray($models,$ignore=null)
    {
        $relations= $this->relations();
        $related=array();
        foreach ($relations as $k=>$r)
        {
            $related[$k]=$k;
        }
        return array_map(function($record) use ($related,$ignore) {
            return  $record->getAttributesWithRelations(true,$ignore);
        }, $models);
    }

    protected function beforeSave()
    {
        $this->setDiffAttributes();
        if (Utility::isNotEmpty($this->getAttributes())) {
            foreach ($this->getAttributes() as $key => $row) {
                if(is_string($row)) {
                    $this->setAttribute($key,trim($row));
                }
            }
        }
        return parent::beforeSave();
    }

    /**
     * 判断并设置差异属性
     */
    public function setDiffAttributes()
    {
        if($this->isNewRecord)
            $this->diffAttributes=$this->attributes;
        else
            $this->diffAttributes= array_diff_assoc($this->oldAttributes,$this->attributes);
    }

    /**
     * 保存
     * @param bool $runValidation
     * @param null $attributes  需要保存的字段，为空则是所有
     * @return boolean whether the saving succeeds
     * @throws Exception
     */
    public function save($runValidation=true,$attributes=null)
    {
        $falseThrowError=true;//失败时抛出异常
        $n = func_num_args();
        if($n==3)
        {
            $args = func_get_args();
            $falseThrowError=$args[2];
        }

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
            $res=parent::save($runValidation,$attributes);
            if(!$res)
                throw new Exception("ActiveRecord Save False");

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return $res;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                $this->errorMessage=$e->getMessage();
                if($falseThrowError)
                    throw $e;
                else
                    return false;
            }
            else
                throw $e;
        }
    }

    /**
     * 删除当前实例对象
     * @param bool $falseThrowError 失败时抛出异常
     * @return bool
     * @throws Exception
     */
    public function delete()
    {
        $falseThrowError=true;
        $n = func_num_args();
        if($n==1)
        {
            $args = func_get_args();
            $falseThrowError=$args[0];
        }
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

            $res=parent::delete();
            if(!$res)
                throw new Exception("ActiveRecord Delete False");
            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return $res;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                if($falseThrowError)
                    throw $e;
                else
                    return false;
            }
            else
                throw $e;
        }
    }

    protected function afterFind()
    {
        $this->oldAttributes=$this->attributes;
        parent::afterFind();
    }

    /**
     * 获取指定属性的原值
     * @param $name
     * @return mixed|null
     */
    public function getOldAttribute($name)
    {
        if(empty($name))
            return null;
        return $this->oldAttributes[$name];

    }

    /**
     * 获取值发生变化的属性
     * @return array
     */
    public function getDiffAttributes()
    {
        return $this->diffAttributes;
    }

    /**
     * 获取当前对象的数据变更记录，一般在save后调用，
     *  返回的格式为["fieldName1"=>["oldValue"=>"","newValue"=>""],"fieldName2"=>["oldValue"=>"","newValue"=>""]]
     * @return array
     */
    public function getUpdateLog()
    {
        $data=array();
        foreach ($this->diffAttributes as $k=>$v)
        {
            $data[$k]["oldValue"]=$this->oldAttributes[$k];
            $data[$k]["newValue"]=$this->attributes[$k];
        }
        return $data;
    }

    /**
     * 获取指定字段的值
     *  第一个参数为数组时，则返回数组中指定字段的值
     *  第二个参数为数组时，则返回数组中不包括指定字段的值
     * @param bool $names
     * @param null $ignore
     * @return mixed
     */
    public function getAttributes($names=true,$ignore=null)
    {
        /*if(is_array($names))
        {
            if(is_array($names["ignore"]))
            {

            }
        }*/
        $attributes=parent::getAttributes($names);
        $defaultIgnore=array("errorMessage","oldAttributes","diffAttributes");

        if(is_array($ignore))
        {
            $defaultIgnore=array_merge($ignore,$defaultIgnore);
        }

        foreach ($defaultIgnore as $v)
        {
            unset($attributes[$v]);
        }
        return $attributes;
    }

    /**
     * Sets the attribute values in a massive way.
     * @param array $values
     * @param bool $safeOnly
     */
    public function setAttributes($values,$safeOnly=false)
    {
        parent::setAttributes($values,$safeOnly);
    }

    /**
     * 指定属性是否修改
     * @param $name
     * @return bool
     */
    public function attributeIsModified($name)
    {
        return $this->getOldAttribute($name)!==$this->getAttribute($name);
    }

}