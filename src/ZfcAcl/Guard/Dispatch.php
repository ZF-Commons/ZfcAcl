<?php

namespace ZfcAcl\Guard;

use Zend\Mvc\MvcEvent;
use ZfcAcl\Exception\UnauthorizedException;
use ZfcAcl\Model\Mapper\DispatchableResourceMapperInterface;
use ZfcAcl\Service\Acl as AclService;
use ZfcAcl\Service\ZfcAclAwareInterface;

/**
 * Dispatch guard applies ACL checks the controller that has been requested
 */
class Dispatch implements Guard, ZfcAclAwareInterface
{
    /**
     * @var AclService
     */
    protected $aclService;

    /**
     * @var DispatchableResourceMapperInterface
     */
    protected $dispatchableResourceMapper;

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
        $controller = $e->getRouteMatch()->getParam('controller', 'not-found');
        if (!$controller) {
            // Can't check against null
            return;
        }
        $controllerResource = $this->dispatchableResourceMapper->getDispatchableResource($controller);

        if (!$this->aclService->isAllowed($controllerResource)) {
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