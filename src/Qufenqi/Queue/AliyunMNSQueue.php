<?php namespace Qufenqi\Queue;

use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Job as JobContract;
use AliMNS\Client;
use Config;

class AliyunMNSQueue extends Queue implements JobContract {

    private $client;
    private $queueMap;

    public function __construct($config = [])
    {
        $config = Config::get('mns');
        $this->client = new Client($config['baseuri'], $config['key'], $config['secret']);
        $this->queueMap = $config['queue'];
    }

	/**
	 * Push a new job onto the queue.
	 *
	 * @param  string  $job
	 * @param  mixed   $data
	 * @param  string  $queue
	 * @return mixed
	 */
	public function push($job, $data = '', $queue = null)
	{
        $payload = $this->createPayload($job, $data);
        return $this->pushRaw($payload, $queue);
	}

	/**
	 * Push a raw payload onto the queue.
	 *
	 * @param  string  $payload
	 * @param  string  $queue
	 * @param  array   $options
	 * @return mixed
	 */
	public function pushRaw($payload, $queue = null, array $options = array())
	{
        $this->client->setQueue($this->getQueue($queue));
        return $this->client->publish($payload);
	}

	/**
	 * Push a new job onto the queue after a delay.
	 *
	 * @param  \DateTime|int  $delay
	 * @param  string  $job
	 * @param  mixed   $data
	 * @param  string  $queue
	 * @return mixed
	 */
	public function later($delay, $job, $data = '', $queue = null)
	{
        $seconds = $this->getSeconds($delay);
        $payload = $this->createPayload($job, $data);
        $this->client->setQueue($this->getQueue($queue));

        return $this->client->publish($payload, $seconds);
	}

	/**
	 * Pop the next job off of the queue.
	 *
	 * @param  string  $queue
	 * @return \Illuminate\Queue\Jobs\Job|null
	 */
	public function pop($queue = null)
    {
        $this->client->setQueue($this->getQueue($queue));
        $job = $this->client->consume();
        if (!is_null($job)) {
            return $this->resolveJob($job);
        }
    }

	protected function resolveJob($job)
	{
		return new Jobs\AliyunMNSJob($this->container, $job, $this->client);
	}

    public function getQueue($queue = null)
    {
        if (empty($queue)) {
            return $this->queueMap['default'];
        }

        if (empty($this->queueMap[$queue])) {
            throw new \Exception('Aliyun MNS queue name is not setted');
        }

        return $this->queueMap[$queue];
    }
}
