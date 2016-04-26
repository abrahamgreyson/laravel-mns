<?php namespace Qufenqi\Queue\Jobs;

use AliyunMNS\Client as MnsClient;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Contracts\Queue\Job as JobContract;

class AliyunMNSJob extends Job implements JobContract
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
     * @param  \Illuminate\Container\Container $container
     * @param  MnsClient                       $mns
     * @param   string                         $queue
     * @param  string                          $job
     *
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
     *
     * @return void
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
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();
        //$this->mns->delete();
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int $delay
     *
     * @return void
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
