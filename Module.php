<?php

namespace ZfcAcl;

use Zend\ModuleManager\ModuleManager,
    Zend\EventManager\StaticEventManager,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    Zend\Mvc\ApplicationInterface,
    ZfcBase\Module\ModuleAbstract;

class Module extends ModuleAbstract implements
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

    public function getServiceConfiguration()
    {
        $module = $this;
        return array(
            'factories' => array(
                'Zend\Acl\Acl' => function($sm) {
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
