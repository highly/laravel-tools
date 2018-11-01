<?php

namespace Aixue\Tools\Services\Guzzle\Traits;

use GuzzleHttp\Psr7\Stream;

trait RequestBodyTrait
{
    /**
     * @var string
     */
    protected $url = '';
    
    /**
     * @var array
     */
    protected $options = [];
    
    /**
     * @var string
     */
    protected $url_prefix_http  = 'http://';
    
    /**
     * @var string
     */
    protected $url_prefix_https = 'https://';
    
    /**
     * @param $url
     * @return $this
     */
    public function url($url)
    {
        $this->url = $this->patchUrl(trim($url));
        return $this;
    }
    
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * @param $body
     * @return $this
     */
    public function body($body)
    {
        if (is_array($body)) {
            $body = \json_encode($body);
        }
        $this->options['body'] = $body;
        return $this;
    }
    
    /**
     * @param array $body
     * @return $this
     */
    public function formBody(array $body)
    {
        $this->options['form_params'] = $body;
        return $this;
    }
    
    /**
     * @param $body
     * @return $this
     */
    public function streamBody($body)
    {
        $this->options['body'] = \GuzzleHttp\Psr7\stream_for($body);
        return $this;
    }
    
    /**
     * @return string|Stream
     */
    public function getBody()
    {
        return $this->options['body'];
    }
    
    /**
     * @return array
     */
    public function getFormBody()
    {
        return $this->options['form_params'];
    }
    
    /**
     * @param array $header
     * @return $this
     */
    public function header(array $header)
    {
        $this->options['headers'] = array_merge($this->options['headers'], $header);
        return $this;
    }
    
    /**
     * @return $this
     */
    public function plainHeader()
    {
        $this->options['headers']['Content-Type'] = 'text/plain';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function jsonHeader()
    {
        $this->options['headers']['Content-Type'] = 'application/json';
        return $this;
    }
    
    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->options['headers'];
    }
    
    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * @param $second
     * @return $this
     */
    public function timeout($second)
    {
        $this->options['timeout'] = (float) $second;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->options['timeout'];
    }
    
    /**
     * @param $url
     * @return string
     */
    protected function patchUrl($url)
    {
        if (strpos($url, $this->url_prefix_http) === false && strpos($url, $this->url_prefix_https) === false) {
            $url = $this->url_prefix_http . $url;
        }
        return $url;
    }
}