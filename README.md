# ZfcAcl - Acl module for Zend Framework 2 applications

Provides ACL and different types of "guards" for your application.

Version: 0.1
Based on [KapitchiAcl](https://github.com/kapitchi/KapitchiAcl)

Author:  [Matus Zeman (matuszemi)](https://github.com/matuszemi)
Skype: matuszemi
Email: matus.zeman@gmail.com
IRC: matuszemi @ irc://irc.freenode.net#zftalk.2

## Features

 * Acl management (Config adapter - using module.config.php)
    * Roles (including hierarchies) [COMPLETE]
    * Resources (including hierarchies) [COMPLETE]
    * Allow/deny rules [COMPLETE]
 * Application services
    * Acl [COMPLETE]
 * Guards
    * Route - protects Mvc routes [COMPLETE]
    * Event - protects events [COMPLETE]
    * Dispatch - protects controllers/dispatchables [COMPLETE]
 * Dynamic ACL [TBD]
 * ACL cache mechanism [TBD]
 * Db adapters (Zend\Db) for all above [NOT STARTED]
 * Config -> Db sync [NOT STARTED]

## Requirements

 * [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
 * [ZendSkeletonApplication](https://github.com/zendframework/ZendSkeletonApplication) (latest master)
 * [ZfcBase](https://github.com/ZF-Commons/ZfcBase) (latest master)

## Installation

 1. Put the module into `/vendor` folder and activate in your `config/application.config.php`.
 2. Implement your own `ZfcAcl\Service\Acl\RoleProvider` (or use existing modules [KapitchiIdentity](https://github.com/kapitchi/KapitchiIdentity) or [ZfcUser](https://github.com/ZF-Commons/ZfcUser)).
 3. Set up static ACL with your own roles, resources, rules as you need (see "Roles, resources and rules" below).
 4. (Optionally) Set up route/event/dispatch guards (see "Guards" below) which do provide implicit protection to your application.
 5. Use the ACL service like `$aclService->isAllowed('ZfcAcl', 'use')`

## Usage

You can manage roles, resources and what rules is loaded into ACL using the `module.config.php` file (`Zend\Di` configuration) from your module or `config/autoload`.

The module is shipped with the `config/ZfcAcl.global.config.php.dist` file, which you can rename to `ZfcAcl.global.config.php` and put in your `config/autoload` directory.
This configuration provides a few pre-defined roles/resources/rules that allow quick start when working with the standard ZendSkeletonApplication.

## Options

See [`config/module.config.php`](https://github.com/ZF-Commons/ZfcAcl/blob/master/config/module.config.php#L4) for all available options.

## RoleProvider

The `RoleProvider` is responsible for getting the currently active `Zend\Acl\Role` in your application.
This module does not manage user roles, it just provides `ZfcAcl\Service\Acl\Role\GenericRoleProvider`as a dummy role provider.
Other modules are responsible for assigning a `ZfcAcl\Service\Acl\RoleProvider` in your acl service.
You can either do so through `Zend\Di` config (example below) or by interacting with the service itself.

As an example, see an implementation of such provider in [KapitchiIdentity module - ZfcAcl RoleProvider](https://github.com/kapitchi/KapitchiIdentity/blob/master/src/KapitchiIdentityAcl/Service/RoleProvider.php).

```php
<?php
// File: MyModule/config/module.config.php
return array(
    'di' => array(
        'instance' => array(
            'ZfcAcl\Service\Acl' => array(
                'parameters' => array(
                    'roleProvider' => 'MyModule\Plugin\ZfcAcl\RoleProvider'
                ),
            ),
        ),
    ),
);
```

## Roles, resources and rules

If you copied your `config/ZfcAcl.global.config.php.dist` to `config/autoload/ZfcAcl.global.config.php`, following roles are introduced.
It is  up to you to define hierarchies or more complex ACL rules.

 * `guest` - anonymous/non authenticated user 
 * `auth` - authenticated user but with no local user reference 
 * `user` - authenticated user with local user reference 

The idea behind `auth` role is that some applications might not need to manage users locally (so there is no local user reference/id known) but they still want users to be authenticated to unlock few parts of the application.
This can be used to render few extra "social" blocks on you site while you're authenticated using Facebook Connect.
In this case you might want to consider creating new role auth/facebook and set Facebook related permissions to this role.
`user` role is considered as having local user reference managed by your user/authentication module.


### Acl configuration

ACL can be fully configured from module config using DI settings of [AclLoaderConfig mapper](https://github.com/ZF-Commons/ZfcAcl/blob/master/src/ZfcAcl/Model/Mapper/AclLoaderConfig.php).
An example can be found in [KapitchiIdentity module](https://github.com/kapitchi/KapitchiIdentity/blob/master/config/acl.config.php).
The mapper reads config array defining roles/resources/rules in the structure below.

#### Config example

Refer to [`config/ZfcAcl.global.config.php.dist`](https://github.com/ZF-Commons/ZfcAcl/blob/master/config/ZfcAcl.global.config.php.dist) for a configuration example.

## Guards

Guards "protect" different aspects of the application from being accessible by unauthorized roles.
If unauthorized roles try to access/trigger a route/controller/event they are not permitted to, a `ZfcAcl\Exception\UnauthorizedException` is thrown.

### Route guard

`ZfcAcl\Guard\Route` is used to protect MVC routes. The guard configuration maps route names to their ACL resource ids.
These resources can then be used in `ZfcAcl\Service\Acl`.

The guard is attached to `Zend\Mvc\Application.dispatch` event at priority of 1000.

#### Config example

Refer to [`config/ZfcAcl.global.config.php.dist`](https://github.com/ZF-Commons/ZfcAcl/blob/master/config/ZfcAcl.global.config.php.dist) for a configuration example.

### Dispatch guard

`ZfcAcl\Guard\Dispatch` is used to protect MVC dispatchables (controllers).
The guard uses a mapper to get the resource ids for the requested controllers.
The default mapper simply converts `$controllerName` to `"controller/$controllerName"`.
These ACL resource ids must be defined in your ACL first.

The guard is attached to `Zend\Mvc\Application.dispatch` event at priority of 1000.

#### Config example

Refer to [`config/ZfcAcl.global.config.php.dist`](https://github.com/ZF-Commons/ZfcAcl/blob/master/config/ZfcAcl.global.config.php.dist) for a configuration example.

### Event guard

`ZfcAcl\Guard\Event` listens through the `StaticEventManager` (with priority 1000) to all events specified in its configuration.
Similar to the Route guard, Event guard maps event identifier (event target) and event name to a resource and privilege optionally.
Resource role permissions can be then adjusted as needed in ACL configuration.

Example of common usage can be an authorizing certain roles to perform application service operations.

E.g. you can specify that only `admin` role can `persist` `MyModule\Model\Album` model.
The condition obviously is to trigger an event before insert/update operation from a service method.

Here is an example of how events would be mapped:

```php
<?php
// File: MyModule/config/module.config.php

$eventGuardDefMapConfig = array(
    'MyModule/Model/Album.get' => array(
        'eventId' => 'MyModule\Service\Album',
        'event' => 'get.load',
        'resource' => 'MyModule/Model/Identity',
        'privilege' => 'get',
    ),
    'MyModule/Model/Album.persist' => array(
        'eventId' => 'MyModule\Service\Album',
        'event' => 'persist.pre',
        'resource' => 'MyModule/Model/Identity',
        'privilege' => 'persist',
    ),
    'MyModule/Model/Album.remove' => array(
        'eventId' => 'MyModule\Service\Album',
        'event' => 'remove.pre',
        'resource' => 'MyModule/Model/Identity',
        'privilege' => 'remove',
    ),
);

$aclConfig = array(
    'resources' => array(
        'MyModule/Model/Identity' => null
    ),
    'rules' => array(
        'allow' => array(
            //grand admin a permission to perform any operation on MyModule/Model/Album resource
            'MyModule/allow/admin' => array('admin', 'MyModule/Model/Album'),
            //grand user a permission to read/get MyModule/Model/Album resource but they can't persist/remove
            'MyModule/allow/user' => array('user', 'MyModule/Model/Album', 'get'),
         ),
    ),
);

return array(
    'di' => array(
        'instance' => array(
            'ZfcAcl\Model\Mapper\AclLoaderConfig' => array(
                'parameters' => array(
                    'config' => $aclConfig
                 )
            ),
            'ZfcAcl\Model\Mapper\EventGuardDefMapConfig' => array(
                'parameters' => array(
                    'config' => $eventGuardDefMapConfig
                 )
            )
        )
    )
);
```

## Dynamic ACL

Example of dynamic ACL can be found in the [ZfcAcl plugin for KapitchiIdentity module](https://github.com/kapitchi/KapitchiIdentity/blob/master/src/KapitchiIdentityAcl/Plugin/ZfcAcl.php).

## Application services

 * `ZfcAcl\Service\Acl` - provides simple interaction with a wrapped `Zend\Acl\Acl` instance.
 * `ZfcAcl\Service\Context` - allows interaction with `ZfcAcl\Service\Acl` with a custom role.

## Events

 * `ZfcAcl\Service\Acl.getAcl` - triggered when ACL is requested. This allows lazy loading of ACL from cache or DB for example. If an ACL instance is provided by a listener, ACL is considered to be fully loaded.
 * `ZfcAcl\Service\Acl.loadStaticAcl` - triggered if no ACL was retrieved during `ZfcAcl\Service\Acl.getAcl`.
 * `ZfcAcl\Service\Acl.staticAclLoaded` - triggered after loading of static ACL (after `ZfcAcl\Service\Acl.loadStaticAcl`).
 * `ZfcAcl\Service\Acl.invalidateCache` - triggered when cache invalidation is needed.
