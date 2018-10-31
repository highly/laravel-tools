# aixue/laravel-notification

## Installation

```composer
composer require aixue/laravel-notification
```

## Generate Config

> for laravel version >= 5.5

```php
php artisan vendor:publish --tag=notification-config
```

> for laravel version < 5.5

```php
cp <PROJECT_PATH>/vendor/aixue/laravel-notification/config/aixue-notification.php <PROJECT_PATH>/config/
```

Add following items to `<PROJECT_PATH>/config/app.php`

```php
'providers' => [
    ...,
    Aixue\Notification\ServiceProvider::class,
]

'aliases' => [
    ...,
    'Notification' => Aixue\Notification\Facades\NotificationFacade::class,
]
```

## Config Example

```php
    /*
    |--------------------------------------------------------------------------
    | DingTalk Config List
    |--------------------------------------------------------------------------
    |
    */
    'dingTalk' => [
        'hook_list' => [
            '<hook_name_1>' => [
                'webhook' => env('ENV_NAME_1'),
            ],
            '<hook_name_2>' => [
                'webhook' => env('ENV_NAME_2'),
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Lark Config List
    |--------------------------------------------------------------------------
    |
    | bot_token  => ['用于获取用户信息等API请求', '申请聊天机器人可获得']
    |
    */
    'lark' => [
        'bot_token' => env('LARK_API_BOT_TOEKN'),
        'hook_list' => [
            '<hook_name_1>' => [
                'webhook' => env('ENV_NAME_1'),
            ],
            '<hook_name_2>' => [
                'webhook' => env('ENV_NAME_2'),
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | SMS Config
    |--------------------------------------------------------------------------
    |
    | code   => 验证码类短信通道
    | notice => 通知类短信通道
    */
    'sms' => [
        'host'    => env('SMS_HOST'),
        'channel' => [
            'code' => [
                'app_id'  => env('SMS_CODE_APP_ID'),
                'app_key' => env('SMS_CODE_APP_KEY'),
            ],
            'notice' => [
                'app_id'  => env('SMS_NOTICE_APP_ID'),
                'app_key' => env('SMS_NOTICE_APP_KEY'),
            ],
        ],
    ],
```

## Code Example

### Lark

> TextMessage

```php
$message = (new TextMessage('title', "content_line_1 \n content_line_2"))
               ->atEmails('shawn@highly.com');
// or using array instead
$message = (new TextMessage('title', ['content_line_1', 'content_line_2']))
               ->atEmails(['shawn@highly.com', 'shawn2@highly.com']);
           
\Notification::lark('hook_name')->send($message);
```

### Sms

> 验证码类型

```php
// 固定模板, 已经申报, 推荐使用, 送达率稳定
$message = (new CodeMessage('1234'))->phoneList(['138xxxxxxxx', '139xxxxxxxx']);
\Notification::Sms()->send($message);

// 自定义验证码内容
$message = (new CodeMessage('自定义验证码内容:, 1234, 请勿泄露.', false))
               ->phoneList('138xxxxxxxx,139xxxxxxxx');
\Notification::Sms()->send($message);
```

> 通知类型

```php
$content = '尊敬的使用者, 您使用的是通知类型短信.';
$message = (new NoticeMessage($content))->phoneList(['138xxxxxxxx', '139xxxxxxxx']);
\Notification::Sms()->send($message);
```

### DingTalk

> TextMessage

```php
$msg = "# Title Info \n\n content_line_1 \n content_line_2";
// or using array instead
$msg = [
    '# Title Info',
    '',
    'content_line_1',
    'content_line_2',
];
$message = (new TextMessage($msg))->atMobiles(['138xxxxxxxx']);
                                //->atAll(true);
\Notification::dingTalk("hook_name")->send($message);
```

> MarkdownMessage

```php
$msg = "## Title Info \n\n"
     . "> content_line_1 \n\n > content_line_2 \n\n"
     . "##### ending_line";
// or using array instead
$msg = [
    '## Title Info',
    '',
    '> content_line_1',
    '',
    '> content_line_2',
    '', '',
    '##### ending_line',
];
$message = (new MarkdownMessage("Title Info", $msg));
\Notification::dingTalk("hook_name")->send($message);
```

> LinkMessage

```php
$message = new LinkMessage(
    "Title_line_1 \nTitle_line_2",
    "content_line_1 \ncontent_line_2 \ncontent_line_3",
    'http://picture_url',
    'http://website_url'
);
// or using array instead
$message = new LinkMessage(
    ['Title_line_1', 'Title_line_2'],
    ['content_line_1', 'content_line_2', 'content_line_3'],
    'http://picture_url',
    'http://website_url'
);
\Notification::dingTalk("hook_name")->send($message);
```

> WholeActionCardMessage

```php
$message = new WholeActionCardMessage('pop title', 'text', 'text title', 'http://website_url');
\Notification::dingTalk("hook_name")->send($message);
```

> IndependenceActionCardMessage

```php
$btn = [
    new Btn('article_1', 'http://website_url_1'),
    new Btn('article_2', 'http://website_url_2'),
];
$message = new IndependenceActionCardMessage('pop title', 'title', $btn);
\Notification::dingTalk("hook_name")->send($message);
```

> FeedCardMessage

```php
$links = [
    new Link('title_1', 'http://messageURL', 'http://picURL'),
    new Link('title_2', 'http://messageURL', 'http://picURL'),
];
\Notification::dingTalk("hook_name")->send(new FeedCardMessage($links));
```