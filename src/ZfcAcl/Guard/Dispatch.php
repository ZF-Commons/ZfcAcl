<?php

namespace ZfcAcl\Guard;

use Zend\Mvc\MvcEvent,
    ZfcAcl\Exception\UnauthorizedException,
    ZfcAcl\Model\Mapper\DispatchableResourceMap,
    ZfcAcl\Service\Acl as AclService;

/**
 * Dispatch guard applies ACL checks the controller that has been requested
 */
class Dispatch implements Guard
{
    /**
     * @var DispatchableResourceMap
     */
    protected $dispatchableResourceMap;

    /**
     * @var AclService
     */
    protected $aclService;

    public function dispatch(MvcEvent $e)
    {
        // @todo this logic should somehow be shared with Zend\Mvc\Application
        $controller = $e->getRouteMatch()->getParam('controller', 'not-found');
        if (!$controller) {
            // Can't check against null
            return;
        }
        $controllerResource = $this->dispatchableResourceMap->getControllerResource($controller);

        if (!$this->aclService->isAllowed($controllerResource)) {
            throw new UnauthorizedException(
                $this->aclService->getRole()->getRoleId() . ' is not allowed to access dispatchable '
                . $controller . ' (' . $controllerResource . ')'
            );
        }
    }

    public function getDispatchableResourceMap()
    {
        return $this->dispatchableResourceMap;
    }

    public function setDispatchableResourceMap(DispatchableResourceMap $dispatchableResourceMap)
    {
        $this->dispatchableResourceMap = $dispatchableResourceMap;
    }

    public function getAclService()
    {
        return $this->aclService;
    }

    public function setAclService(AclService $aclService)
    {
        $this->aclService = $aclService;
    }
}