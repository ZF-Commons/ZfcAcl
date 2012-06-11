<?php

namespace ZfcAcl\Model;

use Zend\EventManager\Event,
    ZfcBase\Model\AbstractModel;

abstract class EventGuardDefTriggeredEventAware extends AbstractModel implements EventGuardDef
{
    protected $eventId;
    protected $event;
    protected $resource;
    protected $privilege;
    protected $triggeredEvent;
    protected $options;

    public function getEventId()
    {
        return $this->eventId;
    }

    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event)
    {
        $this->event = $event;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    public function getPrivilege()
    {
        return $this->privilege;
    }

    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getTriggeredEvent()
    {
        return $this->triggeredEvent;
    }

    public function setTriggeredEvent(Event $triggeredEvent)
    {
        $this->triggeredEvent = $triggeredEvent;
    }

}