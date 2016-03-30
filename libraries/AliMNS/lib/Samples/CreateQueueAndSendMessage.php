<?php
require_once(dirname(dirname(__FILE__)).'/mns-autoloader.php');

use AliyunMNS\Client;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Exception\MnsException;

class CreateQueueAndSendMessage
{
    private $accessId;
    private $accessKey;
    private $endPoint;
    private $client;

    public function __construct($accessId, $accessKey, $endPoint)
    {
        $this->accessId = $accessId;
        $this->accessKey = $accessKey;
        $this->endPoint = $endPoint;
    }

    public function run()
    {
        $queueName = "shop-error-notification";

        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);

        // 1. create queue
        $request = new CreateQueueRequest($queueName);
        try
        {
            $res = $this->client->createQueue($request);
            echo "QueueCreated! \n";
        }
        catch (MnsException $e)
        {
            echo "CreateQueueFailed: " . $e;
            return;
        }
        $queue = $this->client->getQueueRef($queueName);

        // 2. send message
        $messageBody = "test";
        // as the messageBody will be automatically encoded
        // the MD5 is calculated for the encoded body
        $bodyMD5 = md5(base64_encode($messageBody));
        $request = new SendMessageRequest($messageBody);
        try
        {
            $res = $queue->sendMessage($request);
            echo "MessageSent! \n";
        }
        catch (MnsException $e)
        {
            echo "SendMessage Failed: " . $e;
            return;
        }

        // 3. receive message
        $receiptHandle = NULL;
        try
        {
            $res = $queue->receiveMessage();
            echo "ReceiveMessage Succeed! \n";
            if (strtoupper($bodyMD5) == $res->getMessageBodyMD5())
            {
                echo "You got the message sent by yourself! \n";
            }
            $receiptHandle = $res->getReceiptHandle();
        }
        catch (MnsException $e)
        {
            echo "ReceiveMessage Failed: " . $e;
            return;
        }

        // 4. delete message
        try
        {
            $res = $queue->deleteMessage($receiptHandle);
            echo "DeleteMessage Succeed! \n";
        }
        catch (MnsException $e)
        {
            echo "DeleteMessage Failed: " . $e;
            return;
        }

        // 5. delete queue
        try {
            $this->client->deleteQueue($queueName);
            echo "DeleteQueue Succeed! \n";
        } catch (MnsException $e) {
            echo "DeleteQueue Failed: " . $e;
            return;
        }
    }
}

$accessId = "49IcnE0L6jgvLceF";
$accessKey = "mVAbbYcqM7qmF0Eogiqn7TIKqCdkrl";
$endPoint = "http://1551977665808533.mns.cn-hangzhou.aliyuncs.com";

if (empty($accessId) || empty($accessKey) || empty($endPoint))
{
    echo "Must Provide AccessId/AccessKey/EndPoint to Run the Example. \n";
    return;
}

$instance = new CreateQueueAndSendMessage($accessId, $accessKey, $endPoint);
$instance->run();

?>
