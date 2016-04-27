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

namespace Qufenqi\Queue\Jobs;

use AliyunMNS\Client as MnsClient;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;

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
     * @var
     */
    private $mns;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Container\Container $container
     * @param MnsClient                       $mns
     * @param string                          $queue
     * @param string                          $job
     */
    public function __construct(Container $container, $mns, $queue, $job)
    {
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
                "Seems it's not a Laravel enqueued job. [$body]"
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
        //$this->mns->delete();
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay
     */
    public function release($delay = 0)
    {
        parent::release($delay);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
    }
}
