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
    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
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
        return array(
            'factories' => array(
            ),
        );
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
