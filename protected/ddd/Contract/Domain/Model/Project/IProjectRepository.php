<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/10 11:55
 * Describe：
 */

namespace ddd\Contract\Domain\Model\Project;


use ddd\Common\Domain\IRepository;

interface IProjectRepository extends IRepository
{

    function saveCannotBack(Project $project);

    function submit(Project $project);

    function trash(Project $project);

    function reject(Project $project);
}