<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/21 17:37
 * Describe：
 */

namespace ddd\Contract\Domain\Model\Project;


use ddd\Common\BaseId;

class ProjectId extends BaseId
{
    /**
     * 获取新的id
     * @return ProjectId
     */
    public static function generate()
    {
        return new static(\IDService::getProjectId());
    }

}