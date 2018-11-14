<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:44
 * Describe：
 */

namespace ddd\Contract\Domain\Model\Project;


use ddd\infrastructure\DIService;

trait ProjectRepository
{
    /**
     * @var IProjectRepository
     */
    protected $projectRepository;

    /**
     * 获取项目仓储
     * @return IProjectRepository
     * @throws \Exception
     */
    protected function getProjectRepository()
    {
        if (empty($this->projectRepository))
        {
            $this->projectRepository=DIService::getRepository(IProjectRepository::class);
        }
        return $this->projectRepository;
    }
}