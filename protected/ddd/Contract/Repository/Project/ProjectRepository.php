<?php
/**
 * Created by vector.
 * DateTime: 2018/3/26 11:33
 * Describe：项目仓储
 */

namespace ddd\Contract\Repository\Project;

use ddd\Contract\Domain\Model\Project\Project;
use ddd\Common\IAggregateRoot;
use ddd\Contract\Domain\Model\Project\IProjectRepository;
use ddd\Contract\Domain\Model\Project\ProjectDetail;
use ddd\Contract\Domain\Model\Project\ProjectGoods;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\Common\Repository\EntityRepository;


class ProjectRepository extends EntityRepository implements IProjectRepository
{
    public function init()
    {
        $this->with = array("base","base.goods");
    }

    public function getActiveRecordClassName()
    {
        return "Project";
    }

    /**
     * @return Project
     * @throws \Exception
     */
    public function getNewEntity()
    {
        return new Project();
    }



    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $values=$model->getAttributes();
        unset($values["project_id"]);
        $entity->setAttributes($values);
        $entity->setId($model->project_id);
        $entity->is_can_back=($model->is_can_back==1);

        if(!empty($model->base))
        {
            $projectDetail=new ProjectDetail();
            $projectDetail->setAttributes($model->base->getAttributes());
            $entity->detail=$projectDetail;

            if(is_array($model->base->goods))
            {
                foreach ($model->base->goods as $d)
                {
                    $projectGoods=new ProjectGoods();
                    $projectGoods->setAttributes($d->getAttributes());
                    $projectGoods->quantity=new Quantity($d->quantity,$d->unit);
                    $entity->detail->addGoodsItem($projectGoods);
                }
            }
        }

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param Project $entity
     * @return Project
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        $id = $entity->getId();
        if (!empty($id))
        {
            $model = \Project::model()->with($this->with)->findByPk($id);
        }
        if (empty($model))
        {
            $model = new \Project();
        }
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);

        $isNew = $model->isNewRecord;

        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);
        $entity->setId($model->getPrimaryKey());

        $detail=new \ProjectDetail();
        if(!empty($model->base))
            $detail=$model->base;

        $values = $entity->detail->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $detail->setAttributes($values);
        $detail->project_id=$model->project_id;
        $res = $detail->save();
        if (!$res)
            throw new ZModelSaveFalseException($detail);


        $items = $entity->detail->goods_items;
        if (!is_array($items))
            $items = array();
        if (!$isNew)
        {
            if (is_array($model->base->goods))
            {
                foreach ($model->base->goods as $d)
                {
                    if (isset($items[$d->goods_id]))
                    {
                        $item = $items[$d->goods_id];
                        $itemValues = $item->getAttributes();
                        $itemValues = \Utility::unsetCommonIgnoreAttributes($itemValues);
                        $itemValues["quantity"]=$item->quantity->quantity;
                        $itemValues["unit"]=$item->quantity->unit;
                        unset($itemValues["detail_id"]);
                        $d->setAttributes($itemValues);
                        $res = $d->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($d);
                        unset($items[$d->goods_id]);
                    }
                    else
                    {
                        $res = $d->delete();
                        if (!$res)
                            throw new ZModelDeleteFalseException($d);
                    }
                }
            }
        }


        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $d = new \ProjectBaseGoods();
                $itemValues = $item->getAttributes();
                unset($itemValues["detail_id"]);
                $itemValues = \Utility::unsetCommonIgnoreAttributes($itemValues);
                $itemValues["quantity"]=$item->quantity->quantity;
                $itemValues["unit"]=$item->quantity->unit;

                $d->setAttributes($itemValues);

                $d->project_id = $model->project_id;
                $d->base_id=$detail->base_id;

                $res = $d->save();
                if (!$res)
                    throw new ZModelSaveFalseException($d);

            }
        }


        return $entity;
    }

    /**
     * 保存项目设置为不可驳回
     * @param Project $project
     * @throws \Exception
     */
    public function saveCannotBack(Project $project)
    {
        // TODO: Implement saveCannotBack() method.
        $projectId = $project->getId();
        $model = \Project::model()->findByPk($projectId);
        if (empty($model))
        {
            throw new ZModelNotExistsException($projectId, "Project");
        }

        $model->is_can_back = $project->isCanBack()?1:0;
        $model->update_user_id = \Utility::getNowUserId();
        $model->update_time = \Utility::getDateTime();

        $res= $model->update(array("is_can_back", "update_user_id", "update_time"));
        if(!$res)
            throw new ZModelSaveFalseException($model);
    }

    /**
     * 保存提交
     * @param Project $project
     * @throws \Exception
     */
    public function submit(Project $project)
    {
        // TODO: Implement submit() method.
        $this->updateStatus($project);
    }

    /**
     * 保存提交
     * @param Project $project
     * @throws \Exception
     */
    public function trash(Project $project)
    {
        // TODO: Implement submit() method.
        $this->updateStatus($project);
    }

    /**
     * 保存提交
     * @param Project $project
     * @throws \Exception
     */
    public function reject(Project $project)
    {
        // TODO: Implement submit() method.
        $this->updateStatus($project);
    }


    /**
     * @param Project $project
     * @throws \Exception
     */
    protected function updateStatus(Project $project)
    {
        $model = \Project::model()->findByPk($project->project_id);
        if (empty($model))
        {
            throw new ZModelNotExistsException($project->project_id, "Project");
        }
        $model->status=$project->status;
        $model->status_time=$project->status_time;
        $res= $model->update(array("status", "status_time"));
        if(!$res)
            throw new ZModelSaveFalseException($model);
    }


}