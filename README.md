Zend Framework 2 - ZF-Commons ACL module
=================================================
Version: 0.1  
Author:  [Matus Zeman (matuszemi)](https://github.com/matuszemi)

Based on [KapitchiAcl](https://github.com/kapitchi/KapitchiAcl), part of Zf-Commons since 22/03/2012.

Skype: matuszemi  
Email: matus.zeman@gmail.com  
IRC: matuszemi @ FreeNode / #zftalk.2  


Introduction
============
Provides ACL and different types of "guards" for your application.


Features
========

* Acl management (Config adapter - using module.config.php)
  * Roles (including hierarchies) [COMPLETE]
  * Resources (including hierarchies) [COMPLETE]
  * Allow/deny rules [COMPLETE]
* Application services
  * Acl [COMPLETE]
* Guards
  * Route - protects Mvc routes [COMPLETE]
  * Event - protects events [COMPLETE]     
* Dynamic ACL [TBD]
* ACL cache mechanism [TBD]
* Db adapters (Zend\Db) for all above [NOT STARTED]
* Config -> Db sync [NOT STARTED]

Requirements
============

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* [ZfcBase](https://github.com/ZF-Commons/ZfcBase) (latest master)

Usage
=====
You can manage roles, resources and what rules is loaded into ACL using module.config.php file (DI configuration) from your module.
The module comes with few pre-defined roles/resources/rules (see [module config](https://github.com/ZF-Commons/ZfcAcl/blob/master/config/module.config.php)).
ACL module depends on other modules in order to provide role of currently logged in user otherwise it defaults to _guest_ role (see "ZfcAcl\Service\Acl.getRole" event description below).
See an example in [KapitchiIdentity module - ZfcAcl plugin](https://github.com/kapitchi/KapitchiIdentity/blob/master/src/KapitchiIdentity/Plugin/ZfcAcl.php).

ZfcUser integration [TBD]

Options
-------
See [module config](https://github.com/ZF-Commons/ZfcAcl/blob/master/config/module.config.php#L4) for all options available.

Application services
--------------------

### ZfcAcl\Service\Acl

This is only one service delivered by this module with three public methods:

* isAllowed(resource, privilege) - check if current user is allowed to resource/privilege
* getRole() - returns current role of the user
* invalidateCache() - TBD invalidates ACL cache (if enabled in options) - should be called when ACL for the user might change e.g. they login, logout, ...

Roles, resources and rules
--------------------------
ACL module introduces following common roles below. There are no specific permissions/hierarchy set for them as it should be responsibility of your application/modules to do so.

* guest - anonymous/non authenticated user 
* auth - authenticated user but with no local user reference 
* user - authenticated user with local user reference 

The idea behind _auth_ role is that some applications might not need to manage users locally (so there is no local user reference/id known) but they still want users to be authenticated to unlock few parts of the application.
This can be used to render few extra "social" blocks on you site while you're authenticated using Facebook Connect. In this case you might want to consider creating new role auth/facebook and set Facebook related permissions to this role.
_user_ role is considered as having local user reference managed by your user/authentication module.


### Acl configuration
ACL can be fully configured from module config using DI settings of [AclLoaderConfig mapper](https://github.com/ZF-Commons/ZfcAcl/blob/master/src/ZfcAcl/Model/Mapper/AclLoaderConfig.php).
Nice example can be found in [KapitchiIdentity module](https://github.com/kapitchi/KapitchiIdentity/blob/master/config/module.config.php) - search for "ZfcAcl\Model\Mapper\AclLoaderConfig".
The mapper reads config array defining roles/resources/rules in the structure below.

#### Config example
```
File: MyModule/config/module.config.php

$aclConfig = array(
    'roles' => array(
        'admin' => null,
        'role1' => null,
        'role2_with_one_parent' => 'user',
        'role3_with_multiple_parents' => array('guest', 'auth'),
    )
    'resources' => array(
        'parent' => array(
            'child1' => null,
            'child2_with_more_children' => array(
                'grandchild1' => null,
                'grandchild2' => null,
            ),
        ),
    ),
    'rules' => array(
        'allow' => array(
            //grand admin all privileges on any resource
            'allow_rule_unique_identifier' => array('admin', null),
            //grand user all privileges on child1 resource
            'allow_rule_unique_identifier2' => array('user', 'child1'),
            //grand user persist privilege to both grandchild1 and grandchild2 resources
            'allow_rule_unique_identifier3' => array('user', array('grandchild1', 'grandchild2'), 'persist'),
            //grand role1 remove and create privileges on parent resource 
            'allow_rule_unique_identifier4' => array('role1', 'parent', array('remove', 'create')),
        ),
        'deny' => array(
            //same format as for allow rules
        ),
    ),
)

return array(
    'di' => array(
        'instance' => array(
            'ZfcAcl\Model\Mapper\AclLoaderConfig' => array(
                'parameters' => array(
                    'config' => $aclConfig
                 )
            )
        )
    )
);   
```


Guards
------
Guards "protect" different aspects of the application from being accessible by unauthorized users.
If unauthorized user tries to access e.g. route they are not permitted to [Unauthorized exception](https://github.com/ZF-Commons/ZfcAcl/blob/master/src/ZfcAcl/Exception/UnauthorizedException.php) is thrown.  
They have been two guards implemented so far: Route and Event guards.

TBD: Should we return HttpResponse instead of throwing Unauthorized exception?

### Route guard
[Route guard](https://github.com/ZF-Commons/ZfcAcl/blob/master/src/ZfcAcl/Guard/Route.php) is used to protect MVC routes. The guard configuration maps route into ACL route resource. Route resource ACL can be then configured as any other resource permissions.

The guard is attached to Zend\Mvc\Application.dispatch event at priority 1000.

#### Config example

Zend\Mvc\Router\RouteStack route configuration:

* MyModule
    * ChildRoute1
    * ChildRoute2
        * GrandChildRoute1
        * GrandChildRoute2   

See [ZF2 MVC Routing manual](http://packages.zendframework.com/docs/latest/manual/en/zend.mvc.routing.html) for more details or [KapitchiIdentity module example](https://github.com/kapitchi/KapitchiIdentity/blob/master/config/module.config.php).


```
File: MyModule/config/module.config.php


$routeResourceMapConfig = array(
    'default' => 'Route' //sets default route resource - any unresolved routes defaults to 'Route' resource
    'child_map' => array(
        'MyModule' => array(
            //sets 'MyModule/Route' resource being default resource for all child routes under MyModule route
            'default' => 'MyModule/Route',
            'child_map' => array(
                //sets 'MyModule/Route/ChildRoute1' resource for 'ChildRoute1' route
                'ChildRoute1' => 'MyModule/Route/ChildRoute1',
                'ChildRoute2' => array(
                    'default' => 'MyModule/Route/ChildRoute2'
                    'child_map' => array(
                        'GrandChildRoute1' => 'MyModule/Route/ChildRoute2/GrandChild1',
                        'GrandChildRoute2' => 'MyModule/Route/ChildRoute2/GrandChild2',
                     )
                 )
             )
        )
    )
);

$aclConfig = array(
    'resources' => array(
        'Route' => array(
            'MyModule/Route' => array(
                'MyModule/Route/ChildRoute1' => null,
                'MyModule/Route/ChildRoute2' => array(
                    'MyModule/Route/ChildRoute2/GrandChild1' => null,
                    'MyModule/Route/ChildRoute2/GrandChild2' => null,
                ),
            ),
        )
    ),
    'rules' => array(
        'allow' => array(
            //grand admin access permission to all pages/routes
            'allow/default_route' => array('admin', 'Route'),
            //grand user access permission to all pages under MyModule routes
            'MyModule/allow/route2' => array('user', 'MyModule/Route'),
         ),
        'deny' => array(
            //restrict user to access all pages under MyModule/Route/ChildRoute1 and MyModule/Route/ChildRoute2/GrandChild1 routes
            'MyModule/deny/restrict_user' => array('user', array('MyModule/Route/ChildRoute1', 'MyModule/Route/ChildRoute2/GrandChild1')),
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
            'ZfcAcl\Model\Mapper\RouteResourceMapConfig' => array(
                'parameters' => array(
                    'config' => $routeResourceMapConfig
                 )
            )
        )
    )
);   

```                                                  

### Event guard

[Event guard](https://github.com/ZF-Commons/ZfcAcl/blob/master/src/ZfcAcl/Guard/Event.php) protects (at priority 1000) all events specified in the configuration. Similar to Route guard, Event guard maps event identifier (object which triggers the event - a target) and event name to a resource and privilege optionally.
Resource role permissions can be then adjusted as needed in ACL configuration.

Example of common usage can be an authorizing certain roles to perform application service operations.
E.g. you can specify that only _admin_ role can _persist_ _MyModule\Model\Album_ model. The condition obviously is to trigger an event before insert/update operation from a service method.

The guard is attached to all events specified in the configuration at priority 1000. We use StaticEventManager to attach listeners to events.

```
File: MyModule/config/module.config.php


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

Dynamic ACL
------------------------
TBD


Events
------

### ZfcAcl\Service\Acl.getRole

This event is used to retrieve role of currently logged in user. It expects listener to return Zend\Acl\Role instance; if none current role defaults to _guest_.

Triggers until: Zend\Acl\Role

Parameters: none


### ZfcAcl\Service\Acl.getAcl

Used to retrieve Zend\Acl\Acl instance from e.g. caching mechanism. Acl returned is considered being fully loaded with roles, resources, rules.
If none is returned Zend\Acl\Acl instance is created and ZfcAcl\Service\Acl.loadStaticAcl and ZfcAcl\Service\Acl.staticAclLoaded events are triggered.

Triggers until: Zend\Acl\Acl

Parameters:

* roleId - roleId of the user e.g. guest, user...

### ZfcAcl\Service\Acl.staticAclLoaded

This event is trigger once ACL has been loaded. Can be used e.g. by caching mechanism to store ACL into session.

Parameters:

* acl - Zend\Acl\Acl object
* roleId - roleId of the user e.g. guest, user...


### ZfcAcl\Service\Acl.invalidateCache

Triggered when ZfcAcl\Service\Acl::invalidateCache() is called manually.

TBD: do we need auto invalidation of cached ACL? E.g. every 5 mins?

Parameters:

* roleId - roleId of the user e.g. guest, user...


### TBD

ZfcAcl\Service\Acl.loadStaticAcl

This is used to load up static Acl.


array(
                'acl' => $acl,
                'roleId' => $roleId,
            )


'resolveResource', array(
                'resource' => $resource
            )


'loadResource', array(
                'acl' => $acl,
                'roleId' => $roleId,
                'resource' => $resource,
                'privilage' => $privilege
            )
