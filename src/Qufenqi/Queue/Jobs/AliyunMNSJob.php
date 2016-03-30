<?php namespace Qufenqi\Queue\Jobs;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\Job;

class AliyunMNSJob extends Job {

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

    private $client;

	/**
	 * Create a new job instance.
	 *
	 * @param  \Illuminate\Container\Container  $container
	 * @param  string  $job
	 * @param  object  $receiptHandle
	 * @return void
	 */
	public function __construct(Container $container, $job, $client = null)
	{
		$this->job = $job;
		$this->container = $container;
        $this->client = $client;
	}

	/**
	 * Fire the job.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->resolveAndFire(json_decode($this->getRawBody(), true));
	}

	/**
	 * Get the raw body string for the job.
	 *
	 * @return string
	 */
	public function getRawBody()
	{
        return $this->job;
	}

	/**
	 * Delete the job from the queue.
	 *
	 * @return void
	 */
	public function delete()
	{
		parent::delete();
        $this->client->delete();
	}

	/**
	 * Release the job back into the queue.
	 *
	 * @param  int   $delay
	 * @return void
	 */
	public function release($delay = 0)
	{
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
