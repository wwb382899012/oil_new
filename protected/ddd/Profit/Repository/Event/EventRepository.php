<?php
/**
 * Created by vector.
 * DateTime: 2018/8/29 14:36
 * Describe：
 */

namespace ddd\Profit\Repository\Event;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\BaseRepository;
use ddd\Profit\Domain\Event\Event;
use ddd\Profit\Domain\Event\IEventRepository;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelSaveFalseException;

class EventRepository extends BaseRepository implements IEventRepository
{

    /**
     * [findByEventIdAndType 根据事件id和事件类型查找事件]
     * @param
     * @param  [bigint] $eventId [事件id]
     * @param  [int] $type    [事件类型]
     * @return [Event]
     */
    public function findByEventIdAndType($eventId, $type)
    {
        $model=\Event::model()->find("event_id=".$eventId." and event_type=".$type);
        if(empty($model))
            return null;
        
        return $this->dataToEntity($model);
    }

    /**
     *
     * @param \Event $model
     * @return Event
     */
    protected function dataToEntity(\Event $model)
    {
        $entity = new Event();
        $entity->setAttributes($model->getAttributes());
        return $entity;
    }


    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return EstimateContractProfit
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        if(empty($entity))
            throw new ZException("Event实体对象不存在");
        
        $model = new \Event();
        $model->event_id   = $entity->event_id;
        $model->event_name = $entity->event_name;
        $model->event_type = $entity->event_type;

        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);

        return $entity;
    }

}