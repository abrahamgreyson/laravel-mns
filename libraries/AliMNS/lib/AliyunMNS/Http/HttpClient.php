<?php
namespace AliyunMNS\Http;

use AliyunMNS\Config;
use AliyunMNS\Constants;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\BaseRequest;
use AliyunMNS\Responses\BaseResponse;
use AliyunMNS\Signature\Signature;
use AliyunMNS\AsyncCallback;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\TransferException;
use AliyunMNS\Responses\MnsPromise;

class HttpClient
{
    private $client;
    private $accessId;
    private $accessKey;

    public function __construct($endPoint, $accessId,
        $accessKey, Config $config = NULL)
    {
        if ($config == NULL)
        {
            $config = new Config;
        }
        $this->accessId = $accessId;
        $this->accessKey = $accessKey;
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $endPoint,
            'defaults' => [
                'headers' => [
                    'Host' => $endPoint
                ],
                'proxy' => $config->getProxy(),
                'expect' => $config->getExpectContinue(),
                'timeout' => $config->getRequestTimeout()
            ]
        ]);
    }

    private function addRequiredHeaders(BaseRequest &$request)
    {
        $body = $request->generateBody();
        $queryString = $request->generateQueryString();

        $request->setBody($body);
        $request->setQueryString($queryString);

        if ($body != NULL)
        {
            $request->setHeader('Content-Length', strlen($body));
        }
        $request->setHeader('Date', gmdate(Constants::GMT_DATE_FORMAT));
        if (!$request->isHeaderSet('Content-Type'))
        {
            $request->setHeader('Content-Type', 'text/xml');
        }
        $request->setHeader(Constants::MNS_VERSION_HEADER, Constants::MNS_VERSION);

        $sign = Signature::SignRequest($this->accessKey, $request);
        $request->setHeader(Constants::AUTHORIZATION,
            Constants::MNS . " " . $this->accessId . ":" . $sign);
    }

    public function sendRequestAsync(BaseRequest $request,
        BaseResponse &$response, AsyncCallback $callback = NULL)
    {
        $promise = $this->sendRequestAsyncInternal($request, $response, $callback);
        return new MnsPromise($promise, $response);
    }

    public function sendRequest(BaseRequest $request, BaseResponse &$response)
    {
        $promise = $this->sendRequestAsync($request, $response);
        return $promise->wait();
    }

    private function sendRequestAsyncInternal(BaseRequest &$request, BaseResponse &$response, AsyncCallback $callback = NULL)
    {
        $this->addRequiredHeaders($request);

        $parameters = array('exceptions' => false, 'http_errors' => false);
        $queryString = $request->getQueryString();
        $body = $request->getBody();
        if ($queryString != NULL) {
            $parameters['query'] = $queryString;
        }
        if ($body != NULL) {
            $parameters['body'] = $body;
        }

        $request = new Request(strtoupper($request->getMethod()),
            $request->getResourcePath(), $request->getHeaders());
        try
        {
            if ($callback != NULL)
            {
                return $this->client->sendAsync($request, $parameters)->then(
                    function ($res) use (&$response, $callback) {
                        try {
                            $response->parseResponse($res->getStatusCode(), $res->getBody());
                            $callback->onSucceed($response);
                        } catch (MnsException $e) {
                            $callback->onFailed($e);
                        }
                    }
                );
            }
            else
            {
                return $this->client->sendAsync($request, $parameters);
            }
        }
        catch (TransferException $e)
        {
            $message = $e->getMessage();
            if ($e->hasResponse()) {
                $message = $e->getResponse()->getBody();
            }
            throw new MnsException($e->getCode(), $message, $e);
        }
    }
}

?>
