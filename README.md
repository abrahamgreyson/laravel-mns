# Laravel-MNS

阿里云消息服务（MNS）的 Laravel 适配，本质上是为 Laravel 的队列增加 MNS 驱动。包含了阿里云 MNS SDK，为了 Laravel 能透明的使用 MNS 而对其作必要的引用。

 > 接触 Laravel 时已经是 5.1 版，没有测试这个包是否能在小于 5.1 下工作。

 > 自 [aliyun_mns](https://github.com/chefxu/aliyun_mns]) 之上修改而来，鉴于缩进、换行和文件命名等代码风格有所差异，所以没有 PR。

 > 阿里云 MNS SDK 不支持 Composer，直接将其涵盖在版本库中，并将其注册到了 `AliyunMNS` 命名空间下。


## 使用步骤

1. composer require abe/laravel-mns

2. 修改 config/queue.php， `connections` 下新增 `mns` 配置：

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

3. 在你常用的 ServiceProvider 中扩展队列驱动：

```php
Queue::extend('mns', function()
{
    return new \LaravelMns\Connectors\MnsConnector();
});
```

4. 正常使用Laravel Queue 即可:
	[https://laravel.com/docs/5.2/queues](https://laravel.com/docs/5.2/queues)

## 贡献
欢迎任何代码修复和改善，请 Fork 后新建分支，修复完成后用该分支合并我，谢谢！

## 许可

MIT



