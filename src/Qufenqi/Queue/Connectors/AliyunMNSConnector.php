<?php namespace Qufenqi\Queue\Connectors;

use AliyunMNS\Client as MnsClient;
use Config;
use Qufenqi\Queue\AliyunMNSQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class AliyunMNSConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array $config
     *
     * @return \Illuminate\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        $config = Config::get('queue.connections.mns');

        return new AliyunMNSQueue(
            new MnsClient($config['endpoint'], $config['key'], $config['secret']),
            $config['default']
        );
    }
}
