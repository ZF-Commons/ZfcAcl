<?php
return array(
    'ZfcAcl' => array(
        'options' => array(
            // enable static acl session cache
            'enable_cache' => false,
            'enable_guards' => array(
                'route' => false,
                'event' => false,
                'dispatch' => true,
            ),
        ),
    ),
    'di' => array(
        'instance' => array(

            // Service providing ACLs
            'ZfcAcl\Service\Acl' => array(
                'parameters' => array(
                    'aclLoader' => 'ZfcAcl\Model\Mapper\AclLoaderConfig',
                ),
            ),

            // Context providing the current role
            'ZfcAcl\Service\Context' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                ),
            ),

            // Factory used to populate the Acl
            'ZfcAcl\Model\Mapper\AclLoaderConfig' => array(
                'parameters' => array(
                    'config' => array(
                        'resources' => array(
                            //'Route/Default' => null,//used by ZfcAcl\Guard\Route
                        ),
                        'roles' => array(
                            'guest' => null,
                            'auth' => null,
                            'user' => null,
                        ),
                        'rules' => array(
                            'allow' => array(
                                //'allow/default_route' => array(array('auth', 'guest'), 'Route/Default'),
                            )
                        )
                    ),
                ),
            ),

            // Route guard
            'ZfcAcl\Guard\Route' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'routeResourceMapMapper' => 'ZfcAcl\Model\Mapper\RouteResourceMapConfig',
                )
            ),

            // Maps routes to resources
            'ZfcAcl\Model\Mapper\RouteResourceMapConfig' => array(
                'parameters' => array(
                    'config' => array(
                        //'default' => 'Route/Default',
                        'child_map' => array(
                            'default' => array(
                                //'default' => 'Route/Default',
                            )
                        )
                    )
                )
            ),

            // Event guard
            'ZfcAcl\Guard\Event' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'eventGuardDefMapper' => 'ZfcAcl\Model\Mapper\EventGuardDefMapConfig',
                )
            ),

            // Dispatch guard
            'ZfcAcl\Guard\Dispatch' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'dispatchableResourceMap' => 'ZfcAcl\Model\Mapper\DispatchableResourceMap',
                )
            ),
        ),
    ),
);
