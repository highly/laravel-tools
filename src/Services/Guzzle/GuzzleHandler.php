<?php

namespace Aixue\Tools\Services\Guzzle;

use Log;
use Aixue\Tools\Services\StructHandler;
use Aixue\Tools\Services\Guzzle\Traits\RequestStructTrait;
use Aixue\Tools\Services\Guzzle\Traits\RequestBodyTrait;

/**
 * Class GuzzleHandler For Requests
 */
class GuzzleHandler extends StructHandler
{
    use RequestBodyTrait, RequestStructTrait;
    
    const METHOD_GET     = 'get';
    const METHOD_POST    = 'post';
    const METHOD_PUT     = 'put';
    const METHOD_DELETE  = 'delete';
    const METHOD_PATCH   = 'patch';
    const METHOD_HEAD    = 'head';
    const METHOD_OPTIONS = 'options';
    
    const METHOD_LIST = [
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_DELETE,
        self::METHOD_PATCH,
        self::METHOD_HEAD,
        self::METHOD_OPTIONS,
    ];
    
    /**
     * @param $method
     * @param $arguments
     * @return RequestResponse
     */
    public function __call($method, $arguments)
    {
        $request_method = strtolower($method);
        if (! in_array($request_method, self::METHOD_LIST, true)) {
            return RequestResponse::createError(
                RequestResponse::STATUS_ERROR_CODE,
                'Call to undefined method ' . $method
            );
        }
        
        try {
            $response = $this->guzzleClient()->{$request_method}($this->getUrl(), $this->getOptions());
        
            return $response->getStatusCode() === RequestResponse::HTTP_STATUS_CODE_SUCCESS
                ? RequestResponse::createSuccess($response->getBody(), $response->getReasonPhrase())
                : RequestResponse::createError($response->getStatusCode(), $response->getReasonPhrase());
        
        } catch (\Exception $e) {
            Log::error(
                'guzzleHandler_request_exception',
                [
                    'code'    => $e->getCode(),
                    'msg'     => $e->getMessage(),
                    'trace'   => $e->getTrace(),
                    'method'  => $request_method,
                    'url'     => $this->getUrl(),
                    'options' => $this->getOptions(),
                ]
            );
            return RequestResponse::createError($e->getCode(), $e->getMessage());
        }
    }
}
