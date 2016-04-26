<?php namespace Qufenqi\Queue\Connectors;

use Config;
use AliyunMNS\Client as MnsClient;
use Qufenqi\Queue\MnsQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Qufenqi\Queue\MnsAdapter;

class MnsConnector implements ConnectorInterface
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
        return new MnsQueue(
            new MnsAdapter(
                new MnsClient($config['endpoint'], $config['key'], $config['secret']),
                $config['queue']
            )
        );
    }
}
