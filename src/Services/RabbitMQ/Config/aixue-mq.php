<?php
return [
    /**
     * |--------------------------------------------------------------------------
     * | RabbitMQ Config List
     * |--------------------------------------------------------------------------
     * |
     */
    'connections' => [
        
        'default' => [
            'host'     => env('RABBITMQ_HOST'),
            'port'     => env('RABBITMQ_PORT', '5672'),
            'user'     => env('RABBITMQ_USER'),
            'password' => env('RABBITMQ_PWD'),
        ],

        '<group_name_2>' => [
            'host'     => env('RABBITMQ_HOST_2'),
            'port'     => env('RABBITMQ_PORT_2', '5672'),
            'user'     => env('RABBITMQ_USER_2'),
            'password' => env('RABBITMQ_PWD_2'),
        ],
    ],

    'exchange' => [
        
        '<Name_1>' => [
            'exchange_name' => env('RABBITMQ_EXCHANGE'),
            'queue_name'    => env('RABBITMQ_QUEUE'),
            'routing_key'   => env('RABBITMQ_ROUTING_KEY'),
            'consumer_tag'  => env('RABBITMQ_CONSUMER_TAG'),
        ],

        '<Name_2>' => [
            'exchange_name' => env('RABBITMQ_EXCHANGE_2'),
            'queue_name'    => env('RABBITMQ_QUEUE_2'),
            'routing_key'   => env('RABBITMQ_ROUTING_KEY_2'),
            'consumer_tag'  => env('RABBITMQ_CONSUMER_TAG_2'),
        ],
        
    ],
];
