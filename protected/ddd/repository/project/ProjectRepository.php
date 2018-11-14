<?php
/**
 * Created by vector.
 * DateTime: 2018/3/26 11:33
 * Describe：项目仓储
 */

namespace ddd\repository\project;

use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\project\Project;
use ddd\domain\iRepository\project\IProjectRepository;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;


class ProjectRepository extends EntityRepository implements IProjectRepository
{
    public function init()
    {
        $this->with = array("base");
    }

    public function getActiveRecordClassName()
    {
        return "Project";
    }

    /**
     * @return \ddd\domain\entity\BaseEntity|Project
     * @throws \ReflectionException
     */
    public function getNewEntity()
    {
        return new Project();
    }



    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();

        $entity->setAttributes($model->getAttributes(), false);

        if(is_array($model->base))
        {
            foreach ($model->base->goods as $d)
            {

            }
        }

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return bool
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {

    }

    /**
     * 保存项目设置为不可驳回
     * @param Project $project
     * @throws \Exception
     */
    function saveCannotBack(Project $project)
    {
        // TODO: Implement saveCannotBack() method.
        $projectId = $project->getId();
        $model = \Project::model()->findByPk($projectId);
        if (empty($model))
        {
            throw new ZModelNotExistsException($projectId, "Project");
        }
        $model->is_can_back = $project->is_can_back;
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