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
     * @param string $url
     * @return string
     */
    public function getUrl($url = '')
    {
        if ($url) {
            return $this->patchUrl(trim($url));
        }
        return $this->url;
    }
    
    /**
     * @param      $body
     * @param bool $serialize
     * @return $this
     */
    public function body($body, $serialize = false)
    {
        if (\is_array($body)) {
            $body = $serialize ? serialize($body) : \json_encode($body);
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
        try{
            $body = \GuzzleHttp\Psr7\stream_for($body);
        } catch (\Exception $e) {
            $body = '';
        }
        $this->options['body'] = $body;
        return $this;
    }
    
    /**
     * @return string|Stream
     */
    public function getBody()
    {
        return $this->options['body'] ?? '';
    }
    
    /**
     * @return array
     */
    public function getFormBody()
    {
        return $this->options['form_params'] ?? [];
    }
    
    /**
     * @param array $header
     * @return $this
     */
    public function header(array $header)
    {
        if (empty($this->options['headers'])) {
            $this->options['headers'] = $header;
            return $this;
        }
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
        return $this->options['headers'] ?? [];
    }
    
    /**
     * @param array $properties
     * @return $this
     */
    public function properties(array $properties)
    {
        $this->options = array_merge($this->options, $properties);
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
        return $this->options['timeout'] ?? 0;
    }
    
    /**
     * @return $this
     */
    public function init()
    {
        $this->url = '';
        $this->options = [];
        return $this;
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