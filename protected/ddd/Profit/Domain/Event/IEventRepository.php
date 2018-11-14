<?php
/**
 * Created by vector.
 * DateTime: 2018/8/29 11:01
 * Describe：
 */

namespace ddd\Profit\Domain\Event;


interface IEventRepository
{
    /**
     * [findByEventIdAndType 根据事件id和事件类型查找事件]
     * @param
     * @param  [bigint] $eventId [事件id]
     * @param  [int] $type    [事件类型]
     * @return [Event]
     */
    public function findByEventIdAndType($eventId, $type);

}