<?php
/**
 * Desc:
 * User: vector
 * Date: 2018/8/29
 * Time: 11:53
 */

namespace ddd\Profit\Domain\Service;



use ddd\Common\Domain\BaseService;
use ddd\Profit\Domain\Event\Event;
use ddd\Profit\Domain\Event\EventRepository;
use ddd\infrastructure\error\ZException;

class EventService extends BaseService
{
    use EventRepository;

    /**
     * [store description]
     * @param
     * @param  [type] $eventId [description]
     * @param  [type] $type    [description]
     * @param  [type] $name    [description]
     * @return [type]
     */
    public function store($eventId, $type, $name, $isRepeat=false)
    {
        $entity = Event::create($eventId, $type, $name);

        $model = \Event::model()->find("event_id=".$entity->event_id." and event_type=".$entity->event_type);
        if(!empty($model) && !$isRepeat)
            throw new ZException($model->event_name."事件已经添加,事件id：".$model->event_id."，事件类型：".$model->event_type);
        
        $this->getEventRepository()->store($entity);
    }
}