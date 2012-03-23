<?php
return array(
    'ZfcAcl' => array(
        'options' => array(
            'enable_cache' => false,//enable static acl session cache 
            'enable_guards' => array(
                'route' => false,//enable route guard
                'event' => false,//enable event guard
            )
        ),
    ),
    'di' => array(
        'instance' => array(
            'ZfcAcl\Service\Acl' => array(
                'parameters' => array(
                    'aclLoader' => 'ZfcAcl\Model\Mapper\AclLoaderConfig',
                ),
            ),
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
            'ZfcAcl\Guard\Route' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'routeResourceMapMapper' => 'ZfcAcl\Model\Mapper\RouteResourceMapConfig',
                )
            ),
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
            'ZfcAcl\Guard\Event' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'eventGuardDefMapper' => 'ZfcAcl\Model\Mapper\EventGuardDefMapConfig',
                )
            ),
        ),
    ),
);
