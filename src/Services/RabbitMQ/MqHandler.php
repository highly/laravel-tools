<?php

namespace Aixue\Tools\Services\RabbitMQ;

use Log;
use Aixue\Tools\Services\RabbitMQ\Traits\MqStructTrait;
use Aixue\Tools\Services\RabbitMQ\Traits\MqMessageTrait;
use Aixue\Tools\Services\RabbitMQ\Traits\ConfigInfoTrait;
use Aixue\Tools\Services\StructHandler;
use Aixue\Tools\Exceptions\RabbitMqException;

/**
 * Class MqHandler For RabbitMQ
 */
class MqHandler extends StructHandler
{
    use ConfigInfoTrait, MqMessageTrait, MqStructTrait;
    
    const CONNECTION_NAME_DEFAULT = 'default';
    
    /**
     * MqHandler constructor.
     */
    public function __construct()
    {
        register_shutdown_function([$this, 'shutdown']);
    }
    
    /**
     * @param callable $callback
     * @param null     $consumer_tag
     * @return bool
     * @throws RabbitMqException
     */
    public function basicConsume(callable $callback, $consumer_tag = null)
    {
        $channelHandler = $this->initMqConnection()->noLimit()->getChannelHandler();
        
        try {
            $channelHandler->basic_consume(
                $this->getQueue(),
                $this->getConsumerTag($consumer_tag),
                $this->getConsumeNoLocalCfg(),
                $this->getConsumeNoAckCfg(),
                $this->getExclusiveCfg(),
                $this->getConsumeNoWaitCfg(),
                $callback
            );
            
            while (count($channelHandler->callbacks)) {
                $channelHandler->wait();
            }
            
            return true;
        } catch (\Exception $e) {
            Log::warning(
                'mq_basicConsume_exception',
                [
                    'code'         => $e->getCode(),
                    'msg'          => $e->getMessage(),
                    'consumer_tag' => $consumer_tag,
                    'info'         => $this->getCfgInfoForLog(),
                ]
            );
            throw new RabbitMqException($e->getMessage(), $e->getCode());
        }
    }
    
    /**
     * @param string $message
     * @param array  $properties
     * @return bool
     * @throws RabbitMqException
     */
    public function basicPublish($message, array $properties = [])
    {
        $channelHandler = $this->initMqConnection()->getChannelHandler();
        
        try {
            $channelHandler->basic_publish(
                $this->createAMQPMessage($message, $properties), $this->getExchange(), $this->getRoutingKey()
            );
            
            return true;
        } catch (\Exception $e) {
            Log::error(
                'mq_basicPatch_publish_exception',
                [
                    'code'       => $e->getCode(),
                    'msg'        => $e->getMessage(),
                    'trace'      => $e->getTrace(),
                    'message'    => $message,
                    'properties' => $properties,
                    'info'       => $this->getCfgInfoForLog(),
                ]
            );
            throw new RabbitMqException($e->getMessage(), $e->getCode());
        }
    }
    
    /**
     * @param array $message_list
     * @param array $properties
     * @return bool
     * @throws RabbitMqException
     */
    public function batchPublish(array $message_list, array $properties = [])
    {
        $channelHandler = $this->initMqConnection()->getChannelHandler();
        
        try {
            foreach ($message_list as $number => $message) {
                $channelHandler->batch_basic_publish(
                    $this->createAMQPMessage($message, $properties), $this->getExchange(), $this->getRoutingKey()
                );
                if ($number % 6 === 0) {
                    $channelHandler->publish_batch();
                }
            }
            $channelHandler->publish_batch();
            
            return true;
        } catch (\Exception $e) {
            Log::error(
                'mq_basicPatch_publish_exception',
                [
                    'code'       => $e->getCode(),
                    'msg'        => $e->getMessage(),
                    'trace'      => $e->getTrace(),
                    'message'    => $message_list,
                    'properties' => $properties,
                    'info'       => $this->getCfgInfoForLog(),
                ]
            );
            throw new RabbitMqException($e->getMessage(), $e->getCode());
        }
    }
    
    public function shutdown()
    {
        try {
            $channelHandler = $this->getChannelHandler();
            if ($channelHandler !== null) {
                $channelHandler->close();
            }
            $connectionHandler = $this->getConnectionHandler();
            if ($connectionHandler !== null && $connectionHandler->isConnected()) {
                $connectionHandler->close();
            }
        } catch (\Exception $e) {}
    }
}
