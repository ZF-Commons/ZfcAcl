<?php

namespace ZfcAcl\Guard;

use Zend\Mvc\MvcEvent;
use ZfcAcl\Exception\UnauthorizedException;
use Exception as NoRouteResourceFoundException;
use ZfcAcl\Service\ZfcAclAwareInterface;

class Route implements Guard, ZfcAclAwareInterface
{
    /**
     * @var AclService
     */
    protected $aclService;
    protected $routeResourceMapMapper;

    public function onRoute(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $routeName = $routeMatch->getMatchedRouteName();

        $map = $this->getRouteResourceMapMapper()->findByRouteName($routeName);
        if ($map === null) {
            return;
        }
        $routeResource = $map->getRouteResource($routeName);
        if ($routeResource === null) {
            //$routeResource = $this->getDefaultRouteResource();
            //matuszemi: TODO what in this case???
            throw new NoRouteResourceFoundException("No route resource found");
        }

        $acl = $this->getAclService();
        if (!$acl->isAllowed($routeResource)) {
            $roleId = $acl->getRole()->getRoleId();
            throw new UnauthorizedException("You ($roleId) are not allowed to access this route '$routeName' ($routeResource)");
        }
    }

    //setters/getters
    public function getRouteResourceMapMapper()
    {
        return $this->routeResourceMapMapper;
    }

    public function setRouteResourceMapMapper($routeResourceMapMapper)
    {
        $this->routeResourceMapMapper = $routeResourceMapMapper;
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