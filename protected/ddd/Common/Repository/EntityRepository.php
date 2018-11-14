<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/28 12:02
 * Describe：
 */

namespace ddd\Common\Repository;

use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\repository\EntityFile;
use system\components\base\Object;

abstract class EntityRepository extends BaseRepository
{
    use EntityFile;



    public $activeRecordClassName;

    //public $entityClassName;

    public $with = null;

    //protected $fieldsMap;


    /**
     * 初始化字段映射，子类继承该方法
     * 数组格式，key为实体属性名，value为对应数据模型的字段名
     */
    protected function getFieldsMap()
    {
        /*return [
            "entity_id"=>"model_id"
        ];*/
        return [];
    }

    /**
     * 根据model设置entity的值
     * @param $entity
     * @param $model
     */
    protected function setEntityValue(&$entity, &$model)
    {
        $maps = $this->getFieldsMap();
        if (is_array($maps) && count($maps) > 0)
        {
            foreach ($maps as $k => $v)
            {
                $entity->$k = $model->$v;
            }
        }
    }

    /**
     * 根据entity设置model的值
     * @param $model
     * @param $entity
     */
    protected function setModelValue(&$model, &$entity)
    {
        $maps = $this->getFieldsMap();
        if (is_array($maps) && count($maps) > 0)
        {
            foreach ($maps as $k => $v)
            {
                $model->$v = $entity->$k;
            }
        }
    }


    /**
     * 获取新的实体对象
     * @return BaseEntity
     */
    abstract public function getNewEntity();

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    abstract public function getActiveRecordClassName();

    #region overriding


    #endregion


    /**
     * 获取数据模型对象
     * @return static active record model instance
     */
    public function model()
    {
        return \CActiveRecord::model($this->getActiveRecordClassName());
    }

    /**
     * @param $id
     * @param string $condition
     * @param array $params
     * @return null|\ddd\Common\Domain\BaseEntity
     */
    public function findByPk($id, $condition = '', $params = array())
    {
        $model = $this->model()->with($this->with)->findByPk($id, $condition, $params);
        if (empty($model))
            return null;
        return $this->dataToEntity($model);

    }

    /**
     * 数据模型转换成业务对象
     *      一般子类需要重写该方法
     * @param $model
     * @return BaseEntity
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        if (!empty($entity))
        {
            $entity->setAttributes($model->getAttributes(), false);
            $this->setEntityValue($entity, $model);
        }
        return $entity;
    }


    public function find($condition = '', $params = array())
    {
        $model = $this->model()->with($this->with)->find($condition, $params);
        if (empty($model))
            return null;
        return $this->dataToEntity($model);
    }

    public function findAll($condition = '', $params = array())
    {
        $models = $this->model()->with($this->with)->findAll($condition, $params);
        if (!is_array($models) || count($models) < 1)
            return array();
        $entities = array();
        foreach ($models as $m)
        {
            $entities[$m->getPrimaryKey()] = $this->dataToEntity($m);
        }
        return $entities;
    }

    /**
     * 持久化数据
     * @param IAggregateRoot $entity
     * @return IAggregateRoot
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        $id = $entity->getId();
        if (!empty($id))
        {
            $model = $this->model()->findByPk($id);
            if (empty($model))
            {
                throw new ZModelNotExistsException($id, $this->getActiveRecordClassName());
            }
        }
        else
        {
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }
        //这里需要处理一下新增时设置主键值的问题
        $model->setAttributes($entity->getAttributes(), false);
        $this->setModelValue($model, $entity);
        /*if($model->isNewRecord)
            $model->setPrimaryKey(null);*/
        if (!$model->save())
            throw new ZModelSaveFalseException($model);
        $entity->setId($model->getPrimaryKey());
        return $entity;
    }

    /**
     * 移动对象
     * @param IAggregateRoot $entity
     * @return mixed
     * @throws \Exception
     */
    public function remove(IAggregateRoot $entity)
    {
        $model = $this->model()->findByPk($entity->getId());
        if (empty($model))
            throw new ZModelNotExistsException($entity->getId(), $this->getActiveRecordClassName());

        return $model->delete();
    }

}