# Laravel-MNS

阿里云消息服务（MNS）的 Laravel 适配，本质上是为 Laravel 的队列增加 MNS 驱动。包含了阿里云 MNS SDK，为了 Laravel 能透明的使用 MNS 而对其作必要的引用。

 > 接触 Laravel 时已经是 5.1 版，没有测试这个包是否能在小于 5.1 下工作。

 > 自 [aliyun_mns](https://github.com/chefxu/aliyun_mns]) 之上修改而来，鉴于缩进、换行和文件命名等代码风格有所差异，无法发 pr。

 > 阿里云 MNS SDK 不支持 Composer，直接将其涵盖在版本库中，并将其注册到了 `AliyunMNS` 命名空间下。


## 安装使用

通过 Composer 安装：

```shell
$ composer require abe/laravel-mns
```

之后在 config/queue.php 中增加 `mns` 配置：

```php
'connections' => [
    'redis' => [
        'driver'     => 'redis',
        'connection' => 'default',
        'queue'      => 'default',
        'expire'     => 60,
    ],

    // 新增阿里云 MNS。
    'mns'   => [
        'driver'   => 'mns',
        'key'      => env('MNS_ACCESS_KEY', 'access-key'),
        'secret'   => env('MNS_SECRET_KEY', 'secret-key'),
        // 外网连接必须启用 https。
        'endpoint' => 'your-endpoint,
        'queue'    => env('MNS_DEFAULT_QUEUE', 'default-queue-name'),
    ],
],
```

并且在你常用的 ServiceProvider 中注册队列驱动：

```php
Queue::extend('mns', function()
{
    return new \LaravelMns\Connectors\MnsConnector();
});
```

正常使用 Laravel Queue 即可：

[https://laravel.com/docs/5.2/queues](https://laravel.com/docs/5.2/queues)


## 许可

MIT



