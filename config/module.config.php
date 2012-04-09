<?php
return array(
    'ZfcAcl' => array(
        'options' => array(
            // enable static acl session cache
            'enable_cache' => false,
            'enable_guards' => array(
                'route'     => false,
                'event'     => false,
                'dispatch'  => false,
            ),
        ),
    ),
    'di' => array(
        'instance' => array(

            // Service providing ACLs
            'ZfcAcl\Service\Acl' => array(
                'parameters' => array(
                    'aclLoader' => 'ZfcAcl\Model\Mapper\AclLoaderConfig',
                    'roleProvider' => 'ZfcAcl\Service\Acl\GenericRoleProvider',
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
                    ),
                ),
            ),

            // Route guard
            'ZfcAcl\Guard\Route' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'routeResourceMapMapper' => 'ZfcAcl\Model\Mapper\RouteResourceMapConfig',
                ),
            ),

            // Maps routes to resources
            'ZfcAcl\Model\Mapper\RouteResourceMapConfig' => array(
                'parameters' => array(
                    'config' => array(
                        // 'route-1' => 'resource-id-for-route-1'
                        // 'child_map' => array(
                        //     'route-1' => array(
                        //         'child-route-1' => 'resource-id-for-child-route-1',
                        //     ),
                        // ),
                    ),
                ),
            ),

            // Event guard
            'ZfcAcl\Guard\Event' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'eventGuardDefMapper' => 'ZfcAcl\Model\Mapper\EventGuardDefMapConfig',
                ),
            ),

            // Dispatch guard
            'ZfcAcl\Guard\Dispatch' => array(
                'parameters' => array(
                    'aclService' => 'ZfcAcl\Service\Acl',
                    'dispatchableResourceMapper' => 'ZfcAcl\Model\Mapper\DispatchableResourceMapper',
                ),
            ),
        ),
    ),
);
