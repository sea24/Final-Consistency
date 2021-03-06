<?php

return [
    'consul' => [
        'address' => '47.106.178.79',
        'port'    => 8500,
        'register' => [
            'ID'                =>'rabbitMq-message',
            'Name'              =>'rabbitMq-message',
            'Tags'              =>['rabbitMq-message'],
            'Address'           =>'47.106.178.79',
            'Port'              =>9800,
            'Check'             => [
                'tcp'      => '47.106.178.79:9800',
                'interval' => '10s',
                'timeout'  => '2s',
            ],
            'Weights'=>[
                'passing'=>7,
                'warning'=>1
            ]
        ],
        'discovery' => [
            'dc' => 'dc1',
            'tag'=>'rabbitMq-message'
        ]
    ],
];