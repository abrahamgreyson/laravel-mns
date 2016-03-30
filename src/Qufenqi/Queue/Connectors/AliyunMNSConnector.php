<?php namespace Qufenqi\Queue\Connectors;

use Qufenqi\Queue\AliyunMNSQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class AliyunMNSConnector implements ConnectorInterface {

	/**
	 * Establish a queue connection.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Queue\QueueInterface
	 */
	public function connect(array $config)
	{
		return new AliyunMNSQueue($config);
	}

}
