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

namespace LaravelMns\Jobs;

use AliyunMNS\Responses\ReceiveMessageResponse;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use LaravelMns\MnsAdapter;

class MnsJob extends Job implements JobContract
{
    /**
     * The class name of the job.
     *
     * @var string
     */
    protected $job;

    /**
     * The queue message data.
     *
     * @var string
     */
    protected $data;

    /**
     * @var \LaravelMns\MnsAdapter
     */
    private $mns;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Container\Container             $container
     * @param \LaravelMns\MnsAdapter                      $mns
     * @param string                                      $queue
     * @param \AliyunMNS\Responses\ReceiveMessageResponse $job
     */
    public function __construct(
        Container $container,
        MnsAdapter $mns,
        $queue,
        ReceiveMessageResponse $job
    ) {
        $this->container = $container;
        $this->mns = $mns;
        $this->queue = $queue;
        $this->job = $job;
    }

    /**
     * Fire the job.
     */
    public function fire()
    {
        $body = json_decode($this->getRawBody(), true);
        if (!is_array($body)) {
            throw new \InvalidArgumentException(
                "Seems it's not a Laravel enqueued job. \r\n
                [$body]"
            );
        }
        $this->resolveAndFire($body);
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->getMessageBody();
    }

    /**
     * Delete the job from the queue.
     */
    public function delete()
    {
        parent::delete();
        $receiptHandle = $this->job->getReceiptHandle();
        $this->mns->deleteMessage($receiptHandle);
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay
     */
    public function release($delay = 0)
    {
        // 默认情况下 Laravel 将以 delay 0 来更改可见性，其预期的是使用队列服务默认的
        // 下次可消费时间，但 Aliyun MNS PHP SDK 的接口要求这个值必须大于 0，
        // 指从现在起，多久后消息变为可消费。
        $delay = 0 !== $delay
            ? $delay
            : $this->fromNowToNextVisibleTime($this->job->getNextVisibleTime());
        parent::release($delay);
        $this->mns->changeMessageVisibility(
            $this->job->getReceiptHandle(),
            $delay
        );
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->job->getDequeueCount();
    }

    /**
     * 从现在起到消息变为可消费的秒数。
     *
     * @param int $nextVisibleTime 下次可消费时的微妙时间戳。
     *
     * @return int
     */
    private function fromNowToNextVisibleTime($nextVisibleTime)
    {
        $nowInMilliSeconds = 1000 * microtime(true);
        $fromNowToNextVisibleTime = $nextVisibleTime - $nowInMilliSeconds;

        return (int) ($fromNowToNextVisibleTime / 1000);
    }

    /**
     * Get the IoC container instance.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
