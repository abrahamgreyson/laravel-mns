<?php

/*
 * Laravel-Mns -- 阿里云消息队列（MNS）的 Laravel 适配。
 *
 * (c) Abraham Greyson <82011220@qq.com>
 *
 * @link: https://github.com/AbrahamGreyson/laravel-mns
 *
 * @license: MIT
 */

namespace Qufenqi\Queue;

use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\SendMessageRequest;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;
use Qufenqi\Queue\Jobs\AliyunMNSJob;

class MnsQueue extends Queue implements QueueContract
{
    /**
     * @var MnsAdapter
     */
    private $mns;

    /**
     * Custom callable to handle jobs.
     *
     * @var callable
     */
    protected $jobCreator;

    public function __construct(MnsAdapter $mns)
    {
        $this->mns = $mns;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string $job
     * @param mixed  $data
     * @param string $queue
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
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $message = new SendMessageRequest($payload);

        $response = $this->mns->setQueue($queue)->sendMessage($message);

        return $response->getMessageId();
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string        $job
     * @param mixed         $data
     * @param string        $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $seconds = $this->getSeconds($delay);
        $payload = $this->createPayload($job, $data);
        $message = new SendMessageRequest($payload, $seconds);
        $response = $this->mns->setQueue($queue)->sendMessage($message);

        return $response->getMessageId();
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     *
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null)
    {
        try {
            $response = $this->mns->setQueue($queue)->receiveMessage();
        } catch (MnsException $e) {
            $response = null;
        }
        if ($response) {
            if ($this->jobCreator) {
                return call_user_func($this->jobCreator, $this->container, $queue, $response);
            } else {
                return new AliyunMNSJob($this->container, $this->mns, $queue, $response);
            }
        }
    }

    protected function resolveJob($job)
    {
        return new Jobs\AliyunMNSJob($this->container, $job, $this->client);
    }
}
