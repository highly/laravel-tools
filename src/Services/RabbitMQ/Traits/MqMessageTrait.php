<?php

namespace Aixue\Tools\Services\RabbitMQ\Traits;

use PhpAmqpLib\Message\AMQPMessage;

trait MqMessageTrait
{
    /**
     * @var int
     */
    public $delivery_mode_persistent = AMQPMessage::DELIVERY_MODE_PERSISTENT;
    
    /**
     * @var string
     */
    public $content_type_json = 'application/json';
    
    /**
     * @var string
     */
    public $content_type_text = 'text/plain';
    
    
    /**
     * @param AMQPMessage $message
     * @return $this
     */
    public function ack(AMQPMessage $message)
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        return $this;
    }
    
    /**
     * @param string $message
     * @param array  $properties
     * @return AMQPMessage
     */
    public function createAMQPMessage($message, array $properties = [])
    {
        return new AMQPMessage($message, $this->getMessageProperties($message, $properties));
    }
    
    /**
     * @param $message
     * @param $properties
     * @return array
     */
    public function getMessageProperties($message, $properties)
    {
        if (empty($properties)) {
            $properties = [
                'content_type'  => $this->isJson($message) ? $this->content_type_json : $this->content_type_text,
                'delivery_mode' => $this->delivery_mode_persistent,
            ];
        }
        return $properties;
    }
    
    /**
     * @param $string
     * @return bool
     */
    public function isJson($string)
    {
        return ! is_null(json_decode($string));
    }
}