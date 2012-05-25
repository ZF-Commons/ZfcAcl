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
);
