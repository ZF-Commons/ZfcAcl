<?php

namespace ZfcAcl;

use Zend\Module\Manager,
    Zend\Mvc\AppContext as Application,
    Zend\EventManager\StaticEventManager,
    Zend\EventManager\EventDescription as Event,
    Zend\Mvc\MvcEvent as MvcEvent,
    ZfcBase\Module\ModuleAbstract;

class Module extends ModuleAbstract
{
    public function bootstrap(Manager $moduleManager, Application $app)
    {
        $locator = $app->getLocator();

        $events = StaticEventManager::getInstance();

        if ($this->getOption('enable_guards.route', true)) {
            $routeProtector = $locator->get('ZfcAcl\Guard\Route');
            $app->events()->attach('dispatch', array($routeProtector, 'dispatch'), 1000);
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

    public function getDir()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

}
