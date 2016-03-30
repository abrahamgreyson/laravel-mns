<?php namespace AliMNS;
require_once(dirname(dirname(__FILE__)).'/AliMNS/lib/mns-autoloader.php');

use AliyunMNS\Client as MNSClient;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Exception\MnsException;

class Client
{
    private $queue;

    private $client;

    private $receiptHandle;

    public function __construct($endPoint, $accessId, $accessKey, $queue = null)
    {
        $this->client = new MNSClient($endPoint, $accessId, $accessKey);
        if (isset($queue)) {
            $this->setQueue($queue);
        }
    }

    public function setQueue($queue)
    {
        $this->queue = $this->client->getQueueRef($queue);
    }

    /**
     * publish
     * 发布一条消息
     *
     * @param mixed $msg
     * @param int $delaySeconds
     * @return void
     */
    public function publish($msg, $delaySeconds = 0)
    {
        $request = new SendMessageRequest($msg, $delaySeconds);
        return $this->queue->sendMessage($request);
    }

    /**
     * consume
     * 消费一条消息
     *
     * @return mixed
     */
    public function consume()
    {
        try {
            $queue = $this->client->getQueueRef($this->queue);
            $res = $this->queue->receiveMessage();
            $this->receiptHandle = $res->getReceiptHandle();
            return $res->getMessageBody();
        } catch (\AliyunMNS\Exception\MessageNotExistException $e) {
            return null;
        }
    }

    public function getReceiptHandle()
    {
        return $this->receiptHandle;
    }

    /**
     * delete
     * 删除一条消息
     *
     * @return boolean
     */
    public function delete()
    {
        if ($this->receiptHandle !== null) {
            $this->queue->deleteMessage($this->receiptHandle);
            $this->receiptHandle = null;
            return true;
        }

        return false;
    }
}
