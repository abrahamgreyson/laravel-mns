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

namespace LaravelMns;

use AliyunMNS\Client as MnsClient;

/**
 * Class MnsAdapter.
 *
 * @method string getQueueName()
 * @method \AliyunMNS\Responses\SetQueueAttributeResponse setAttribute(\AliyunMNS\Model\QueueAttributes $attributes)
 * @method \AliyunMNS\Responses\MnsPromise setAttributeAsync(\AliyunMNS\Model\QueueAttributes $attributes, \AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\GetQueueAttributeResponse getAttribute(\AliyunMNS\Model\QueueAttributes $attributes)
 * @method \AliyunMNS\Responses\MnsPromise getAttributeAsync(\AliyunMNS\Model\QueueAttributes $attributes, \AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\SendMessageResponse sendMessage(\AliyunMNS\Requests\SendMessageRequest $request)
 * @method \AliyunMNS\Responses\MnsPromise sendMessageAsync(\AliyunMNS\Requests\SendMessageRequest $request, \AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\PeekMessageResponse peekMessage()
 * @method \AliyunMNS\Responses\MnsPromise peekMessageAsync(\AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\ReceiveMessageResponse receiveMessage()
 * @method \AliyunMNS\Responses\MnsPromise receiveMessageAsync(\AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\ReceiveMessageResponse deleteMessage(string $receiptHandle)
 * @method \AliyunMNS\Responses\MnsPromise deleteMessageAsync(string $receiptHandle, \AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\ChangeMessageVisibilityResponse changeMessageVisibility(string $receiptHandle, int $visibilityTimeout)
 * @method \AliyunMNS\Responses\BatchSendMessageResponse batchSendMessage(\AliyunMNS\Requests\BatchSendMessageRequest $request)
 * @method \AliyunMNS\Responses\MnsPromise batchSendMessageAsync(\AliyunMNS\Requests\BatchSendMessageRequest $request, \AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\BatchReceiveMessageResponse batchReceiveMessage(\AliyunMNS\Requests\BatchReceiveMessageRequest $request)
 * @method \AliyunMNS\Responses\MnsPromise batchReceiveMessageAsync(\AliyunMNS\Requests\BatchReceiveMessageRequest $request, \AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\BatchPeekMessageResponse batchPeekMessage(\AliyunMNS\Requests\BatchPeekMessageRequest $request)
 * @method \AliyunMNS\Responses\MnsPromise batchPeekMessageAsync(\AliyunMNS\Requests\BatchPeekMessageRequest $request, \AliyunMNS\AsyncCallback $callback = null)
 * @method \AliyunMNS\Responses\BatchDeleteMessageResponse batchDeleteMessage(\AliyunMNS\Requests\BatchDeleteMessageRequest $request)
 * @method \AliyunMNS\Responses\MnsPromise batchDeleteMessageAsync(\AliyunMNS\Requests\BatchDeleteMessageRequest $request, \AliyunMNS\AsyncCallback $callback = null)
 */
class MnsAdapter
{
    /**
     * @var string 适配的阿里云消息服务 SDK 版本，仅用作记录。
     *
     * @see https://help.aliyun.com/document_detail/mns/sdk/php-sdk.html
     */
    const ADAPTER_TO_ALIYUN_MNS_SDK_VERSION = '1.3.0@2016-02-25';

    /**
     * Aliyun MNS SDK Client.
     *
     * @var MnsClient
     */
    private $client;

    /**
     * Aliyun MNS SDK Queue.
     *
     * @var \AliyunMNS\Queue
     */
    private $queue;

    public function __construct(MnsClient $client, $queueName)
    {
        $this->client = $client;
        $this->setQueue($queueName);
    }

    /**
     * 转化 \AliyunMNS\Client 对象，
     * 可以通过本对象直接访问（而无需通过 \AliyunMNS\Client 对象构建）。
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->queue, $method], $parameters);
    }

    /**
     * 将队列设定为特定队列。
     *
     * @param $queue
     *
     * @return self
     */
    public function setQueue($queue)
    {
        if (null !== $queue) {
            $this->queue = $this->client->getQueueRef($queue);
        }

        return $this;
    }
}
