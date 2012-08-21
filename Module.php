<?php

namespace ZfcAcl;

use Zend\ModuleManager\ModuleManager,
    Zend\EventManager\StaticEventManager,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    Zend\Mvc\ApplicationInterface,
    ZfcBase\Module\AbstractModule;

class Module extends AbstractModule implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    protected $application;

    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
        $this->application = $app;
        $locator = $app->getServiceManager();

        if ($this->getOption('enable_guards.route', true)) {
            $routeProtector = $locator->get('ZfcAcl\Guard\Route');
            $app->events()->attach('route', array($routeProtector, 'onRoute'), -1000);
        }

        if ($this->getOption('enable_guards.event', true)) {
            $guard = $locator->get('ZfcAcl\Guard\Event');
            $guard->bootstrap();
        }

        if ($this->getOption('enable_guards.dispatch', true)) {
            $guard = $locator->get('ZfcAcl\Guard\Dispatch');
            $app->events()->attach('dispatch', array($guard, 'dispatch'), 1000);
        }
    }

    public function getServiceConfig()
    {
        $module = $this;
        return array(
            'factories' => array(
                'Zend\Acl\Acl' => function ($sm) {
                    return new \Zend\Acl\Acl();
                },

                'ZfcAcl\Service\Acl' => function ($sm) use ($module) {
                    $service = new Service\Acl($module);
                    $service->setAclLoader(new Model\Mapper\AclLoaderConfig);
                    $service->setRoleProvider(new Service\Acl\GenericRoleProvider);
                    $service->setEventManager($sm->get('EventManager'));
                    $service->setServiceLocator($sm);
                    return $service;
                },

                'ZfcAcl\Service\Context' => function ($sm) {
                    $context = new Service\Context;
                    $context->setAclService($sm->get('ZfcAcl\Service\Acl'));
                    return $context;
                },

                'ZfcAcl\Controller\Plugin\ZfcAcl' => function ($sm) {
                    $plugin = Controller\Plugin\ZfcAcl;
                    $plugin->setAclService($sm->get('ZfcAcl\Service\Acl'));
                    return $plugin;
                },

                'ZfcAcl\View\Helper\ZfcAcl' => function ($sm) {
                    $helper = View\Helper\ZfcAcl;
                    $helper->setAclService($sm->get('ZfcAcl\Service\Acl'));
                    return $helper;
                },

                'ZfcAcl\Model\Mapper\AclLoaderConfig' => function ($sm) {
                    $config = new Model\Mapper\AclLoaderConfig;
                    $config->setConfig(array(
                        'config' => array(
                            'resources' => array(
                                //'Route/Default' => null,//used by ZfcAcl\Guard\Route
                                // 'resource-id-1' => array('parent-resource-id-1', 'parent-resource-id-2'),
                                // 'resource-id-2' => null,
                            ),
                            'roles' => array(
                                // 'role-id-1' => array('parent-role-id-1', 'parent-role-id-2'),
                                // 'role-id-2' => null
                            ),
                            'rules' => array(
                                'allow' => array(
                                    // Keys in these array are just for allowing overrides, the syntax is:
                                    // 'somekey' => array( array('role1', 'role2', 'role3'),'resource-id')
                                ),
                                'deny' => array(
                                ),
                            ),
                        )
                    ));

                    return $config;
                },

                'ZfcAcl\Guard\Route' => function ($sm) {
                    $guard = new Guard\Route;
                    $guard->setAclService($sm->get('ZfcAcl\Service\Acl'));
                    $guard->setRouteResourceMapMapper($sm->get('ZfcAcl\Model\Mapper\RouteResourceMapConfig'));
                    return $guard;
                },

                'ZfcAcl\Model\Mapper\RouteResourceMapConfig' => function ($sm) {
                    $mapper = new Model\Mapper\RouteResourceMapConfig;
                    $mapper->setConfig(array());
                    return $mapper;
                },

                'ZfcAcl\Guard\Event' => function ($sm) {
                    $guard = new Guard\Event;
                    $guard->setAclService($sm->get('ZfcAcl\Service\Acl'));
                    $guard->setEventGuardDefMapper($sm->get('ZfcAcl\Model\Mapper\EventGuardDefMapConfig'));
                    return $guard;
                },

                'ZfcAcl\Model\Mapper\EventGuardDefMapConfig' => function ($sm) {
                    return new Model\Mapper\EventGuardDefMapConfig;
                },

                'ZfcAcl\Guard\Dispatch' => function ($sm) {
                    $mapper = $sm->get('ZfcAcl\Model\Mapper\DispatchableResourceMapper');

                    $guard = new Guard\Dispatch($mapper);
                    $guard->setAclService($sm->get('ZfcAcl\Service\Acl'));
                    return $guard;
                },

                'ZfcAcl\Model\Mapper\DispatchableResourceMapper' => function ($sm) {
                    return new Model\Mapper\DispatchableResourceMapper;
                },
            ),
        );
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function getDir()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

}
