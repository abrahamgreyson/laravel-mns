<?php
namespace AliyunMNS\Model;

use AliyunMNS\Constants;

/**
 * Please refer to
 * https://docs.aliyun.com/?spm=#/pub/mns/api_reference/intro&intro
 * for more details
 */
class TopicAttributes
{
    private $maximumMessageSize;
    private $messageRetentionPeriod;

    # the following attributes cannot be changed
    private $topicName;
    private $createTime;
    private $lastModifyTime;

    public function __construct(
        $maximumMessageSize = NULL,
        $messageRetentionPeriod = NULL,
        $topicName = NULL,
        $createTime = NULL,
        $lastModifyTime = NULL)
    {
        $this->maximumMessageSize = $maximumMessageSize;
        $this->messageRetentionPeriod = $messageRetentionPeriod;

        $this->topicName = $topicName;
        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;
    }

    public function setMaximumMessageSize($maximumMessageSize)
    {
        $this->maximumMessageSize = $maximumMessageSize;
    }

    public function getMaximumMessageSize()
    {
        return $this->maximumMessageSize;
    }

    public function setMessageRetentionPeriod($messageRetentionPeriod)
    {
        $this->messageRetentionPeriod = $messageRetentionPeriod;
    }

    public function getMessageRetentionPeriod()
    {
        return $this->messageRetentionPeriod;
    }

    public function getTopicName()
    {
        return $this->topicName;
    }

    public function getCreateTime()
    {
        return $this->createTime;
    }

    public function getLastModifyTime()
    {
        return $this->lastModifyTime;
    }

    public function writeXML(\XMLWriter $xmlWriter)
    {
        if ($this->maximumMessageSize != NULL)
        {
            $xmlWriter->writeElement(Constants::MAXIMUM_MESSAGE_SIZE, $this->maximumMessageSize);
        }
        if ($this->messageRetentionPeriod != NULL)
        {
            $xmlWriter->writeElement(Constants::MESSAGE_RETENTION_PERIOD, $this->messageRetentionPeriod);
        }
    }

    static public function fromXML(\XMLReader $xmlReader)
    {
        $maximumMessageSize = NULL;
        $messageRetentionPeriod = NULL;
        $topicName = NULL;
        $createTime = NULL;
        $lastModifyTime = NULL;

        while ($xmlReader->read())
        {
            if ($xmlReader->nodeType == \XMLReader::ELEMENT)
            {
                switch ($xmlReader->name) {
                case 'MaximumMessageSize':
                    $xmlReader->read();
                    if ($xmlReader->nodeType == \XMLReader::TEXT)
                    {
                        $maximumMessageSize = $xmlReader->value;
                    }
                    break;
                case 'MessageRetentionPeriod':
                    $xmlReader->read();
                    if ($xmlReader->nodeType == \XMLReader::TEXT)
                    {
                        $messageRetentionPeriod = $xmlReader->value;
                    }
                    break;
                case 'TopicName':
                    $xmlReader->read();
                    if ($xmlReader->nodeType == \XMLReader::TEXT)
                    {
                        $topicName = $xmlReader->value;
                    }
                    break;
                case 'CreateTime':
                    $xmlReader->read();
                    if ($xmlReader->nodeType == \XMLReader::TEXT)
                    {
                        $createTime = $xmlReader->value;
                    }
                    break;
                case 'LastModifyTime':
                    $xmlReader->read();
                    if ($xmlReader->nodeType == \XMLReader::TEXT)
                    {
                        $lastModifyTime = $xmlReader->value;
                    }
                    break;
                }
            }
        }

        $attributes = new TopicAttributes(
            $maximumMessageSize,
            $messageRetentionPeriod,
            $topicName,
            $createTime,
            $lastModifyTime);
        return $attributes;
    }
}

?>
