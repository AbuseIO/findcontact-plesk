<?php

/*
 * Config override to use the findcontact-ripe
 */

return [
    'external' => [
        'prefer_local'                      => true,
        'findcontact'                       => [
            'id' => [
                [
                    'class'                     => 'Plesk',
                    'method'                    => 'getContactById',
                ],
            ],
            'ip' => [
                [
                    'class'                     => 'Plesk',
                    'method'                    => 'getContactByIp',
                ],
            ],
            'domain' => [
                [
                    'class'                     => 'Plesk',
                    'method'                    => 'getContactByDomain',
                ],
            ],
        ],
    ],
];
