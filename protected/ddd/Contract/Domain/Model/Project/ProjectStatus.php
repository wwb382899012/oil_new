<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/16 10:34
 * Describe：
 */

namespace ddd\Contract\Domain\Model\Project;


use ddd\Common\BaseEnum;

class ProjectStatus extends BaseEnum
{
    /**
     * 新增加
     */
    const STATUS_NEW = 0;
    /**
     * 项目撤回
     */
    const STATUS_BACK = 1;
    /**
     * 项目提交
     */
    const STATUS_SUBMIT=10;
}