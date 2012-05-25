<?php

namespace ZfcAcl\Guard;

use Zend\Mvc\MvcEvent,
    ZfcAcl\Exception\UnauthorizedException,
    ZfcAcl\Model\Mapper\DispatchableResourceMapperInterface,
    ZfcAcl\Service\Acl as AclService;

/**
 * Dispatch guard applies ACL checks the controller that has been requested
 */
class Dispatch implements Guard
{
    /**
     * @var DispatchableResourceMapperInterface
     */
    protected $dispatchableResourceMapper;

    /**
     * @var AclService
     */
    protected $aclService;

    /**
     * @param DispatchableResourceMapperInterface $dispatchableResourceMapper
     */
    public function __construct(DispatchableResourceMapperInterface $dispatchableResourceMapper)
    {
        $this->setDispatchableResourceMapper($dispatchableResourceMapper);
    }

    public function dispatch(MvcEvent $e)
    {
        // @todo this logic should somehow be shared with Zend\Mvc\Application
        $routeMatch = $e->getRouteMatch();
        $controller = $routeMatch->getParam('controller', 'not-found');
        $action = $routeMatch->getParam('action', null);
        if (!$controller) {
            // Can't check against null
            return;
        }
        $controllerResource = $this->dispatchableResourceMapper->getDispatchableResource($controller);

        if (!$this->aclService->isAllowed($controllerResource, $action)) {
            throw new UnauthorizedException(
                $this->aclService->getRole()->getRoleId() . ' is not allowed to access dispatchable '
                . $controller . ' (' . $controllerResource . ')'
            );
        }
    }

    /**
     * @return DispatchableResourceMapperInterface
     */
    public function getDispatchableResourceMapper()
    {
        return $this->dispatchableResourceMapper;
    }

    /**
     * @param DispatchableResourceMapperInterface $dispatchableResourceMapper
     */
    public function setDispatchableResourceMapper(DispatchableResourceMapperInterface $dispatchableResourceMapper)
    {
        $this->dispatchableResourceMapper = $dispatchableResourceMapper;
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
