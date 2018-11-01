<?php

namespace Aixue\Tools\Services\Guzzle;

/**
 * Class GuzzleHandler For Requests
 */
class RequestResponse
{
    /**
     * @var int
     */
    private $code;
    
    /**
     * @var string
     */
    private $message;
    
    /**
     * @var array|string
     */
    private $data;
    
    const HTTP_STATUS_CODE_SUCCESS = 200;
    
    const STATUS_SUCCESS_CODE = 0;
    
    const STATUS_SUCCESS_MESSAGE = 'OK';
    
    const STATUS_ERROR_CODE = 1;
    
    const STATUS_ERROR_MESSAGE = 'ERROR';
    
    /**
     * RequestResponse constructor.
     * @param        $code
     * @param        $msg
     * @param string $data
     */
    private function __construct($code, $msg, $data = '')
    {
        $this->code    = $code;
        $this->message = $msg;
        $this->data    = $data;
    }
    
    /**
     * @param string $data
     * @param string $message
     * @return RequestResponse
     */
    public static function createSuccess($data = '', $message = self::STATUS_ERROR_MESSAGE)
    {
        return new static(self::STATUS_SUCCESS_CODE, $message, $data);
    }
    
    /**
     * @param int    $code
     * @param string $message
     * @param string $data
     * @return RequestResponse
     */
    public static function createError($code = self::STATUS_ERROR_CODE, $message = self::STATUS_ERROR_MESSAGE, $data = '')
    {
        $code = (int) $code;
        if ($code === self::STATUS_SUCCESS_CODE) {
            $code = self::STATUS_ERROR_CODE;
        }
        return new static($code, $message, $data);
    }
    
    /**
     * @return bool
     */
    public function success()
    {
        return $this->code === self::STATUS_SUCCESS_CODE;
    }
    
    /**
     * @return bool
     */
    public function fails()
    {
        return $this->code !== static::STATUS_SUCCESS_CODE;
    }
    
    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * @return array|string
     */
    public function getRawBody()
    {
        return $this->data;
    }
    
    /**
     * @return array|string
     */
    public function getBody()
    {
        return (string) $this->data;
    }
    
    /**
     * @return mixed
     */
    public function getArrayBody()
    {
        return \json_decode($this->getBody(), true);
    }
}
