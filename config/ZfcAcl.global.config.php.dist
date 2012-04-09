<?php
/**
 * Local Configuration Override for ZfcAcl
 *
 * This file is shipped to allow easy setup of ZfcAcl with the ZendSkeletonApplication
 * Application module.
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 */

return array(

    'ZfcAcl' => array(
        'options' => array(

            // enable static acl session cache
            'enable_cache' => false,

            'enable_guards' => array(
                // Enabling route and dispatch guards
                'route'     => true,
                'event'     => false,
                'dispatch'  => true,
            ),
        ),
    ),

    'di' => array(
        'instance' => array(
            // Factory used to populate the Acl
            'ZfcAcl\Model\Mapper\AclLoaderConfig' => array(
                'parameters' => array(
                    'config' => array(
                        'resources' => array(

                            // ACL Resource IDs for routes defined in the Application module
                            'Route/Default' => null,
                            'Route/Home' => null,

                            // ACL Resource IDs for controllers defined in the Application module
                            'dispatchable/Application\\Controller\\IndexController' => null,

                        ),
                        'roles' => array(

                            // Configuring default roles
                            'guest' => null,
                            'auth' => null,
                            'user' => null,

                        ),
                        'rules' => array(
                            'allow' => array(

                                // Enabling access to routes defined in Application module to all default roles
                                'allow/Route/Default' => array(array('guest', 'auth', 'user'), 'Route/Default'),
                                'allow/Route/Home' => array(array('guest', 'auth', 'user'), 'Route/Home'),

                                // Enabling access to controllers defined in Application module to all default roles
                                'allow/Application\\Controller\\IndexController' => array(
                                    array('guest', 'auth', 'user'),
                                    'dispatchable/Application\\Controller\\IndexController',
                                ),

                            ),
                        ),
                    ),
                ),
            ),

            'ZfcAcl\Model\Mapper\RouteResourceMapConfig' => array(
                'parameters' => array(
                    'config' => array(

                        // mapping routes to their resource ids
                        'default' => 'Route/Default',
                        'home' => 'Route/Home',

                    ),
                ),
            ),
        ),
    ),
);