# aixue/laravel-tools

## Installation

```composer
composer require aixue/laravel-tools
```

## Config

> for laravel version < 5.5

Add following items to `<PROJECT_PATH>/config/app.php`

```php
'providers' => [
    ...,
    Aixue\Tools\ServiceProvider::class,
]

'aliases' => [
    ...,
    'Tools' => Aixue\Tools\Facades\ToolsFacade::class,
]
```

## Service List

* RabbitMQ
* Requests
* Aliyun Upload


### RabbitMQ

#### Generate Config

> for laravel version >= 5.5

```php
php artisan vendor:publish --tag=mq-config
```

> for laravel version < 5.5

```php
cp <PROJECT_PATH>/vendor/aixue/laravel-tools/src/Services/RabbitMQ/Config/aixue-mq.php <PROJECT_PATH>/config/
```

#### config map

```php
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

        '<connection_name_2>' => [
            'host'     => env('RABBITMQ_HOST_2'),
            'port'     => env('RABBITMQ_PORT_2', '5672'),
            'user'     => env('RABBITMQ_USER_2'),
            'password' => env('RABBITMQ_PWD_2'),
        ],
    ],

    'exchange' => [
        
        '<exchange_group_name_1>' => [
            'exchange_name' => env('RABBITMQ_EXCHANGE'),
            'queue_name'    => env('RABBITMQ_QUEUE'),
            'routing_key'   => env('RABBITMQ_ROUTING_KEY'),
            'consumer_tag'  => env('RABBITMQ_CONSUMER_TAG'),
        ],

        '<exchange_group_name_2>' => [
            'exchange_name' => env('RABBITMQ_EXCHANGE_2'),
            'queue_name'    => env('RABBITMQ_QUEUE_2'),
            'routing_key'   => env('RABBITMQ_ROUTING_KEY_2'),
            'consumer_tag'  => env('RABBITMQ_CONSUMER_TAG_2'),
        ],
        
    ],
];
```

#### basic_publish `single`

```php
// default connection name is 'default'
try{
    \Tools::rabbitMQ('exchange_group_name_1')->basicPublish('MQ Message');
} catch (\Exception $e) {}

// define another connection name
try{
    \Tools::rabbitMQ('exchange_group_name_1', 'group_name_2')->basicPublish('MQ Message');
} catch (\Exception $e) {}
```

#### publish_batch `multiple`

```php
try{
    \Tools::rabbitMQ('exchange_group_name_1')->batchPublish(
        ['MQ Message 1', 'MQ Message 2']
    );
} catch (\Exception $e) {}
```

#### basic_consume `block`

```php
try{
    $mqHandler = \Tools::rabbitMQ('exchange_group_name_1');
    $mqHandler->basicConsume(function(\PhpAmqpLib\Message\AMQPMessage $message) use ($mqHandler) {
        echo "\n--------\n";
        echo $message->body;
        echo "\n--------\n";
        
        // ack back
        // same as: $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        $mqHandler->ack($message);
    });
} catch (\Exception $e) {}
```

### Requests

#### Method GET

```php

```


### Aliyun Upload


