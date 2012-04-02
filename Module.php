<?php

namespace ZfcAcl;

use Zend\Module\Manager,
    Zend\Mvc\AppContext as Application,
    Zend\EventManager\StaticEventManager,
    Zend\EventManager\EventDescription as Event,
    Zend\Mvc\MvcEvent as MvcEvent,
    ZfcBase\Module\ModuleAbstract;

class Module extends ModuleAbstract {
    
    public function bootstrap(Manager $moduleManager, Application $app) {
        $locator      = $app->getLocator();
        
        $events = StaticEventManager::getInstance();
        
        if($this->getOption('enable_guards.route', true)) {
            $routeProtector = $locator->get('ZfcAcl\Guard\Route');
            $app->events()->attach('dispatch', array($routeProtector, 'dispatch'), 1000);
        }
        
        if($this->getOption('enable_guards.event', true)) {
            $guard = $locator->get('ZfcAcl\Guard\Event');
            $guard->bootstrap();
        }
        
        if($this->getOption('enable_loaders.resource', true)) {
            $loader = $locator->get('ZfcAcl\Loader\Resource');
            $events->attach('ZfcAcl\Service\Acl', 'loadResource', array($loader, 'onLoadResource'));
        }
    }
    
    public function getDir() {
        return __DIR__;
    }
    
    public function getNamespace() {
        return __NAMESPACE__;
    }
    
}
