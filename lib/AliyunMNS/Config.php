<?php
namespace AliyunMNS;

class Config
{
    //private $maxAttempts;
    private $proxy;  // http://username:password@192.168.16.1:10
    private $requestTimeout;
    private $expectContinue;

    public function __construct()
    {
        // $this->maxAttempts = 3;
        $this->proxy = NULL;
        $this->requestTimeout = 6; // 6 seconds
        $this->expectContinue = false;
    }

    /*
    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }

    public function setMaxAttempts($maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }
    */

    public function getProxy()
    {
        return $this->proxy;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function getRequestTimeout()
    {
        return $this->requestTimeout;
    }

    public function setRequestTimeout($requestTimeout)
    {
        $this->requestTimeout = $requestTimeout;
    }

    public function getExpectContinue()
    {
        return $this->expectContinue;
    }

    public function setExpectContinue($expectContinue)
    {
        $this->expectContinue = $expectContinue;
    }
}

?>
