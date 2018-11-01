<?php

namespace Aixue\Tools\Services\Guzzle\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

trait RequestStructTrait
{
    protected $guzzleHttpClient;
    
    /**
     * @return Client
     */
    public function guzzleClient()
    {
        if (! $this->guzzleHttpClient instanceof Client) {
            $this->guzzleHttpClient = new Client();
        }
        return $this->guzzleHttpClient;
    }
    
    /**
     * @param $method
     * @return Request
     */
    public function getRequest($method)
    {
        return new Request($method, $this->getUrl(), $this->getHeader(), $this->getBody());
    }
}