<?php

namespace ZfcAcl\Guard;

use Zend\EventManager\StaticEventManager;
use Zend\Acl\Resource\ResourceInterface as Resource;
use ZfcAcl\Model\EventGuardDefTriggeredEventAware;
use ZfcAcl\Exception\UnauthorizedException;
use ZfcAcl\Service\ZfcAclAwareInterface;

class Event implements Guard, ZfcAclAwareInterface
{
    /**
     * @var AclService
     */
    protected $aclService;
    protected $eventGuardDefMapper;

    public function bootstrap()
    {
        $events = StaticEventManager::getInstance();
        $acl = $this->getAclService();

        $defMapper = $this->getEventGuardDefMapper();
        $defs = $defMapper->findByRoleId($acl->getRole()->getRoleId());

        foreach ($defs as $def) {
            $events->attach($def->getEventId(), $def->getEvent(), function($e) use ($acl, $def)
            {
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
                    throw new UnauthorizedException(
                        "$roleId` is not allowed to perform '$privilege' on '$resource'"
                    );
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

    /**
     * {@inheritDoc}
     */
    public function setZfcAclService(AclService $acl)
    {
        $this->aclService = $acl;
    }

    /**
     * {@inheritDoc}
     */
    public function getZfcAclService()
    {
        return $this->aclService;
    }
}