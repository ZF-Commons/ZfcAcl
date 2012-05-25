<?php

namespace ZfcAcl\Guard;

use Zend\EventManager\StaticEventManager,
    Zend\Acl\Resource\ResourceInterface as Resource,
    ZfcAcl\Model\EventGuardDefTriggeredEventAware,
    ZfcAcl\Exception\UnauthorizedException;

class Event implements Guard
{
    protected $aclService;
    protected $eventGuardDefMapper;

    public function bootstrap()
    {
        $events = StaticEventManager::getInstance();
        $acl = $this->getAclService();

        $defMapper = $this->getEventGuardDefMapper();
        $defs = $defMapper->findByRoleId($acl->getRole()->getRoleId());

        foreach ($defs as $def) {
            $events->attach($def->getEventId(), $def->getEvent(), function($e) use ($acl, $def) {
                if ($def instanceof EventGuardDefTriggeredEventAware) {
                    $def->setTriggeredEvent($e);
                }

                $resource = $def->getResource();
                $privilege = $def->getPrivilege();
                if (!$acl->isAllowed($resource, $privilege)) {
                    $roleId = $acl->getRole()->getRoleId();
                    if ($resource instanceof Resource) {
                        $resource = $resource->getResourceId();
                    }
                    throw new UnauthorizedException("You ($roleId) are not allowed to perform '$privilege' on '$resource'");
                }
            }, 1000);
        }

    }

    public function getEventGuardDefMapper()
    {
        return $this->eventGuardDefMapper;
    }

    public function setEventGuardDefMapper($eventGuardDefMapper)
    {
        $this->eventGuardDefMapper = $eventGuardDefMapper;
    }

    public function getAclService()
    {
        return $this->aclService;
    }

    public function setAclService($aclService)
    {
        $this->aclService = $aclService;
    }

}