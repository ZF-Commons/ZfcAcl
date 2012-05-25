<?php

namespace ZfcAcl\Service;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Acl\Acl as ZendAcl,
    Zend\Acl\Resource\ResourceInterface as Resource,
    Zend\Acl\Resource\GenericResource,
    Zend\Acl\Role\RoleInterface as Role,
    Zend\Acl\Role\GenericRole,
    Zend\EventManager\Event,
    Zend\Acl\Exception\InvalidArgumentException as ZendAclInvalidArgumentException,
    ZfcBase\Service\ServiceAbstract,
    ZfcAcl\Module,
    ZfcAcl\Model\Mapper\AclLoader,
    ZfcAcl\Service\Acl\RoleProvider,
    InvalidArgumentException as NoStringResourceException,
    RuntimeException as NoRoleProviderException;

class Acl extends ServiceAbstract
{
    protected $module;
    protected $acl;
    protected $aclLoader;
    protected $roleProvider;

    /**
     * TODO XXX there is a bug in DI while injecting a module works with constructor only!
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->setModule($module);
    }

    public function isAllowed($resource = null, $privilege = null)
    {
        $acl = $this->getAcl();
        $roleId = $this->getRoleId();

        //matuszemi: we want to get resource object here at all times
        if (!$resource instanceof Resource) {
            $result = $this->triggerEvent('resolveResource', array(
                'resource' => $resource
            ), function($ret) {
                return $ret instanceof Resource;
            });
            $resolvedResource = $result->last();
            if ($resolvedResource instanceof Resource) {
                $resource = $resolvedResource;
            } else {
                if (!is_string($resource)) {
                    throw new NoStringResourceException("Resource needs to be Resource instance or string");
                }
                $resource = new GenericResource($resource);
            }
        }

        if (!$acl->hasResource($resource)) {

            //mz: this is your chance to load resource ACL up!
            $this->triggerEvent('loadResource', array(
                'acl'       => $acl,
                'roleId'    => $roleId,
                'resource'  => $resource,
                'privilege' => $privilege
            ));

            //mz: we don't want to cache dynamic resources - this should be removed?
            //persist (possibly) update ACL into cache mechanism e.g. session etc.
//            $this->triggerEvent('cacheAcl', array(
//                'acl' => $acl,
//                'roleId' => $roleId,
//                'resource' => $resource,
//                'privilege' => $privilege
//            ));
        }

        try {
            $ret = $acl->isAllowed($roleId, $resource, $privilege);

            return $ret;
        } catch(ZendAclInvalidArgumentException $e) {
            //mz: in case there is still no resource/role
            return false;
        }
    }

    public function getAcl()
    {
        if ($this->acl === null) {
            $roleId = $this->getRoleId();
            $result = $this->events()->trigger('getAcl', $this, array(
                'roleId' => $roleId,
            ), function($ret) {
                return ($ret instanceof ZendAcl);
            });
            $acl = $result->last();
            //is there some plugin which returns acl (e.g. cached in the session?)
            if ($acl instanceof ZendAcl) {
                $this->acl = $acl;
                return $this->acl;
            }

            $acl = $this->getServiceLocator()->get('Zend\Acl\Acl');
            $this->triggerEvent('loadStaticAcl', array(
                'acl' => $acl,
                'roleId' => $roleId,
            ));
            $this->triggerEvent('staticAclLoaded', array(
                'acl' => $acl,
                'roleId' => $roleId,
            ));

            $this->acl = $acl;
        }

        return $this->acl;
    }

    public function invalidateCache()
    {
        $this->triggerEvent('invalidateCache', array(
            'roleId' => $this->getRoleId(),
        ));

        $this->acl = null;
    }

    /**
     * @return Zend\Acl\Role
     */
    public function getRole()
    {
        $roleProvider = $this->getRoleProvider();
        if (!$roleProvider instanceof RoleProvider) {
            throw new NoRoleProviderException("No role provider available");
        }
        $role = $roleProvider->getCurrentRole();
        if (!$role instanceof Role) {
            $role = new GenericRole('guest');
        }

        return $role;
    }

    protected function getRoleId()
    {
        return $this->getRole()->getRoleId();
    }

    //event listeners
    public function getCacheSessionAcl(Event $e)
    {
        //TODO DI!
        $mapper = $this->getServiceLocator()->get('ZfcAcl\Model\Mapper\AclCacheSession');
        $acl = $mapper->loadByRoleId($e->getParam('role'));
        return $acl;
    }

    public function persistCacheSessionAcl(Event $e)
    {
        //TODO DI!
        $mapper = $this->getServiceLocator()->get('ZfcAcl\Model\Mapper\AclCacheSession');
        return $mapper->persist($e->getParam('acl'), $e->getParam('role'));
    }

    public function invalidateCacheSession(Event $e)
    {
        //TODO DI!
        $mapper = $this->getServiceLocator()->get('ZfcAcl\Model\Mapper\AclCacheSession');
        $mapper->invalidate($e->getParam('role'));
    }

    protected function attachDefaultListeners()
    {
        $events = $this->events();

        //AclLoaderMapper
        $aclLoader = $this->getAclLoader();
        $events->attach('loadStaticAcl', function($e) use ($aclLoader) {
            if ($aclLoader instanceof AclLoader) {
                $aclLoader->loadAclByRoleId($e->getParam('acl'), $e->getParam('roleId'));
            }
        });

        if ($this->getModule()->getOption('enable_cache', false)) {
            $events->attach('getAcl', array($this, 'getCacheSessionAcl'));
            $events->attach('staticAclLoaded', array($this, 'persistCacheSessionAcl'));
            $events->attach('invalidateCache', array($this, 'invalidateCacheSessionCache'));
        }
    }

    public function setModule(Module $module)
    {
        $this->module = $module;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getAclLoader()
    {
        return $this->aclLoader;
    }

    public function setAclLoader(AclLoader $aclLoader)
    {
        $this->aclLoader = $aclLoader;
    }

    public function getRoleProvider()
    {
        return $this->roleProvider;
    }

    public function setRoleProvider(RoleProvider $roleProvider)
    {
        $this->roleProvider = $roleProvider;
    }

}
