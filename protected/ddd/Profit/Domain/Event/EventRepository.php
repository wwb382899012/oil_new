<?php
/**
 * Desc: 事件trait
 * User: vector
 * Date: 2018/8/29
 * Time: 10:58
 */

namespace ddd\Profit\Domain\Event;

use ddd\infrastructure\DIService;


trait EventRepository
{
    /**
     * @var IEventRepository
     */
    protected $eventRepository;

    /**
     * @desc 获取事件仓储
     * @return IEventRepository
     * @throws \Exception
     */
    protected function getEventRepository()
    {
        if(empty($this->eventRepository)) {
            $this->eventRepository = DIService::getRepository(IEventRepository::class);
        }

        return $this->eventRepository;
    }
}