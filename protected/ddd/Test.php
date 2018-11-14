<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:38
 * Describe：
 */

namespace ddd;


use ddd\Common\Domain\BaseEvent;
use ddd\Common\Factory;
use ddd\Contract\Domain\Model\Project\IProjectRepository;
use ddd\Contract\Domain\Model\Project\Project;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\domain\tRepository\contract\ContractRepository;
use ddd\infrastructure\DIService;
use system\components\base\Object;

class Test extends Object
{
    use TestDI;



    /**
     * @var IContractRepository
     */
    public $contractRepository;

    /**
     * 获取合同仓储
     * @return IContractRepository
     * @throws \Exception
     */
    protected function getContractRepository()
    {
        if (empty($this->contractRepository))
        {
            $this->contractRepository=DIService::getRepository(IContractRepository::class);
        }
        return $this->contractRepository;
    }
    //use ContractRepository;

    public function test()
    {
       /* if(empty($this->contractRepository))
            $this->getContractRepository();*/
        /*$contract=$this->contractRepository->findByPk(10);
        var_dump($contract);*/
        //Factory::createInstance(Project::class)

        $project=$this->repository->findByPk(20);
        var_dump($this->repository);
        var_dump($project);
    }


    public static function onProjectSubmitted(BaseEvent $event)
    {
        echo $event->sender->getId();
        echo $event->eventName;
    }
}

trait TestDI
{
    private $repository;
    public function __construct(IProjectRepository $projectRepository)
    {
        $this->repository=$projectRepository;
    }

}