<?php namespace Qufenqi\Queue;

use AliyunMNS\Client as MnsClient;
use AliyunMNS\Requests\SendMessageRequest;
use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Qufenqi\Queue\Jobs\AliyunMNSJob;

class AliyunMNSQueue extends Queue implements QueueContract
{
    /**
     * Aliyun MNS Client instance.
     *
     * @var MnsClient
     * @see https://help.aliyun.com/document_detail/mns/sdk/php-sdk.html
     */
    private $mns;

    /**
     * Default queue name.
     *
     * @var string
     */
    private $queue;

    public function __construct(MnsClient $mns, $default)
    {
        $this->mns = $mns;
        $this->queue = $default;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string $job
     * @param  mixed  $data
     * @param  string $queue
     *
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
     * @param  string $payload
     * @param  string $queue
     * @param  array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $message = new SendMessageRequest($payload);
        $queue = $this->setQueue($this->getQueue($queue));
        $response = $queue->sendMessage($message);

        return $response->get('MessageId');
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTime|int $delay
     * @param  string        $job
     * @param  mixed         $data
     * @param  string        $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $seconds = $this->getSeconds($delay);
        $payload = $this->createPayload($job, $data);
        $queue = $this->setQueue($this->getQueue($queue));
        $response = $queue->sendMessage($payload, $seconds);

        return $response->get('MessageId');
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string $queue
     *
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->setQueue($this->getQueue($queue));
        $response = $queue->receiveMessage();

        if (count($response['Messages'] > 0)) {
            if ($this->jobCreator) {
                return call_user_func($this->jobCreator, $this->container, $this->getQueue($queue), $response);
            } else {
                return new AliyunMNSJob($this->container, $this->sqs, $queue, $response['Messages'][0]);
            }
        }
        $job = $this->client->consume();
        if (!is_null($job)) {
            return $this->resolveJob($job);
        }
    }

    public function getQueue($queue = null)
    {
        return $queue ?: $this->queue;
    }

    /**
     * @param $queue
     *
     * @return \AliyunMNS\Queue
     */
    protected function setQueue($queue)
    {
        $queue = $this->mns->getQueueRef($queue);

        return $queue;
    }

    protected function resolveJob($job)
    {
        return new Jobs\AliyunMNSJob($this->container, $job, $this->client);
    }
}
