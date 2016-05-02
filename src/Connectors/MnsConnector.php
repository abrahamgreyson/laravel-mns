<?php

/*
 * Laravel-Mns -- 阿里云消息队列（MNS）的 Laravel 适配。
 *
 * This file is part of the abe/laravel-mns.
 *
 * (c) Abraham Greyson <82011220@qq.com>
 * @link: https://github.com/abrahamgreyson/laravel-mns
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LaravelMns\Connectors;

use AliyunMNS\Client as MnsClient;
use Config;
use Illuminate\Queue\Connectors\ConnectorInterface;
use LaravelMns\MnsAdapter;
use LaravelMns\MnsQueue;

/**
 * Class MnsConnector.
 *
 * @codeCoverageIgnore
 */
class MnsConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = Config::get('queue.connections.mns');

        return new MnsQueue(
            new MnsAdapter(
                new MnsClient($config['endpoint'], $config['key'], $config['secret']),
                $config['queue']
            )
        );
    }
}
