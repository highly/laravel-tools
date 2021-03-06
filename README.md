# aixue/laravel-tools

## Installation

```composer
composer require aixue/laravel-tools
```

You can also declare the dependency for PHP in the `composer.json` file.

```composer
"require": {
  "aixue/laravel-tools": "^1.0"
}
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
            'vhost'    => env('RABBITMQ_VHOST'),
        ],

        '<connection_name_2>' => [
            'host'     => env('RABBITMQ_HOST_2'),
            'port'     => env('RABBITMQ_PORT_2', '5672'),
            'user'     => env('RABBITMQ_USER_2'),
            'password' => env('RABBITMQ_PWD_2'),
            'vhost'    => env('RABBITMQ_VHOST_2'),
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
// the return object of function rabbitMQ is not singleton
$group_one = \Tools::rabbitMQ('exchange_group_name_1');

// default connection name is 'default'
try{
    $group_one->basicPublish('MQ Message 1');
    $group_one->basicPublish('MQ Message 2');
} catch (\Exception $e) {}

// define another connection name
try{
    \Tools::rabbitMQ('exchange_group_name_1', 'connection_name_2')
          ->basicPublish('MQ Message 1');
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

#### Dealing Response

```php
// when failed
if ($response->fails()) {
    // error code > 0
    echo 'error code: ' . $response->getCode();
    // error message
    echo 'error message: ' . $response->getMessage();
}
// when success
if ($response->success()) {
    // response body, rawBody() is available
    echo 'string body: ' . $response->getBody();
    // array body if string body can be json decoded
    var_dump($response->getArrayBody());
}
```

#### Method GET

```php
$response = \Tools::request()
                  ->url('127.0.0.1/test-get?params=one')
                  // set up timeout (unit: second)
                  ->timeout(2)
                  ->header(['X-Foo' => 'Bar'])
                  ->properties([
                      // 'allow_redirects' => false,
                      'allow_redirects' => [
                          'max'             => 10,        // allow at most 10 redirects.
                          'strict'          => true,      // use "strict" RFC compliant redirects.
                          'referer'         => true,      // add a Referer header
                          'protocols'       => ['https'], // only allow https URLs
                          'on_redirect'     => $onRedirect,
                          'track_redirects' => true
                      ],
                  ])
                  ->get();
```

#### Method POST

> Raw body requests

```php
$body = [
    "with_author"   => true,
    "with_brief_id" => [10, 22],
    "item_ids"      => [1233955216, 4823217897],
];

$response = \Tools::request()
                  ->url('10.11.24.123:3433/search/books')
                  // set up timeout (unit: second)
                  ->timeout(2)
                  // create json header for request
                  ->jsonHeader()
                  // create other custom header
                  ->header([
                      'Content-Length' => 1024,
                      'X-Foo'          => 'Bar'
                  ])
                  // auto convert to json
                  ->body($body)
                  // serialize the boby
                  // ->body($body, true)
                  ->post();
```

> Form Requests

```php
$body = [
    'username' => 'form_post',
    'password' => '123456',
];
$response = \Tools::request()
                  ->url('www.shawn.com/login')
                  ->formBody($body)
                  ->post();
```

#### Method PUT

```php
$body = [
    'name' => 'put',
    'age'  => '10000',
];
$response = \Tools::request()
                  ->url('www.shawn.com/update')
                  ->jsonHeader()
                  // ->body(http_build_query($body))
                  ->body($body)
                  ->put();
```

#### Method DELETE

```php
$body = [
    'name' => 'delete',
    'age'  => '0',
];
$response = \Tools::request()
                  ->url('www.shawn.com/delete')
                  ->jsonHeader()
                  ->body($body)
                  ->delete();
```

#### Method PATCH

```php
$body = [
    'name' => 'patch',
    'age'  => '10',
];
$response = \Tools::request()
                  ->url('www.shawn.com/patch')
                  ->jsonHeader()
                  ->formBody($body)
                  ->patch();
```

#### Method HEAD

```php
$body = [
    'name' => 'head',
    'age'  => '10',
];
$response = \Tools::request()
                  ->url('www.shawn.com/head')
                  ->jsonHeader()
                  ->body($body)
                  ->head();
```

#### Method OPTIONS

```php
$body = [
    'name' => 'options',
    'age'  => '101',
];
$response = \Tools::request()
              ->url('www.shawn.com/options')
              ->timeout(2)
              ->jsonHeader()
              ->body($body)
              ->options();
```

### Aliyun Upload


