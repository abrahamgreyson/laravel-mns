<?php namespace Qufenqi\Queue\Connectors;

use Config;
use AliyunMNS\Client as MnsClient;
use Qufenqi\Queue\AliyunMNSQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Qufenqi\Queue\MnsAdapter;

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
            new MnsAdapter(
                new MnsClient($config['endpoint'], $config['key'], $config['secret']),
                $config['queue']
            )
        );
    }
}
