<?php

return [
    'consul' => [
        'address' => '47.106.178.79',
        'port'    => 8500,
        'register' => [
            'ID'                =>'test2',
            'Name'              =>'meiquick',
            'Tags'              =>['ali-test'],
            'Address'           =>'134.175.221.102',
            'Port'              =>7800,
            'Check'             => [
                'tcp'      => '134.175.221.102:7800',
                'interval' => '10s',
                'timeout'  => '2s',
            ],
            'Weights'=>[
                'passing'=>10,
                'warning'=>1
            ]
        ],
        'discovery' => [
            'dc' => 'dc1',
            'tag'=>'ali-test'
        ]
    ],
];