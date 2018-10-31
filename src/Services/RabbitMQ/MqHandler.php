<?php

namespace Aixue\Tools\Services\RabbitMQ;

use Log;
use Aixue\Tools\Services\RabbitMQ\Traits\MqStructTrait;
use Aixue\Tools\Services\RabbitMQ\Traits\MqMessageTrait;
use Aixue\Tools\Services\RabbitMQ\Traits\ConfigInfoTrait;
use Aixue\Tools\Services\StructHandler;

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
     */
    public function basicConsume(callable $callback, $consumer_tag = null)
    {
        if (! $this->initMqConnection()) {
            Log::warning('mq_basicConsume_connection_error', ['info' => $this->getCfgInfoForLog()]);
            return false;
        }
        
        try {
            $channelHandler = $this->getChannelHandler();
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
        } catch (\Exception $e) {
            Log::warning(
                'mq_basicConsume_info', ['consumer_ta' => $consumer_tag, 'info' => $this->getCfgInfoForLog()]
            );
            return false;
        }
        return true;
    }
    
    /**
     * @param string $message
     * @param array  $properties
     * @return bool
     */
    public function basicPublish($message, array $properties = [])
    {
        if (! $this->initMqConnection()) {
            Log::warning(
                'mq_basicPatch_connection_error',
                ['msg' => $message, 'properties' => $properties, 'info' => $this->getCfgInfoForLog()]
            );
            return false;
        }
        
        try {
            $this->getChannelHandler()->basic_publish(
                $this->createAMQPMessage($message, $properties), $this->getExchange(), $this->getRoutingKey()
            );
        } catch (\Exception $e) {
            Log::error(
                'mq_basicPatch_publish_exception',
                [
                    'msg'        => $e->getMessage(),
                    'trace'      => $e->getTrace(),
                    'message'    => $message,
                    'properties' => $properties,
                    'info'       => $this->getCfgInfoForLog(),
                ]
            );
            return false;
        }
        return true;
    }
    
    /**
     * @param array $message_list
     * @param array $properties
     * @return bool
     */
    public function batchPublish(array $message_list, array $properties = [])
    {
        if (! $this->initMqConnection()) {
            Log::warning(
                'mq_batchPublish_connection_error',
                ['msg' => $message, 'properties' => $properties, 'info' => $this->getCfgInfoForLog()]
            );
            return false;
        }
        
        try {
            $channelHandler = $this->getChannelHandler();
            foreach ($message_list as $number => $message) {
                $channelHandler->batch_basic_publish(
                    $this->createAMQPMessage($message, $properties), $this->getExchange(), $this->getRoutingKey()
                );
                if ($number % 6 === 0) {
                    $channelHandler->publish_batch();
                }
            }
            $channelHandler->publish_batch();
        } catch (\Exception $e) {
            Log::error(
                'mq_basicPatch_publish_exception',
                [
                    'msg'        => $e->getMessage(),
                    'trace'      => $e->getTrace(),
                    'message'    => $message_list,
                    'properties' => $properties,
                    'info'       => $this->getCfgInfoForLog(),
                ]
            );
            return false;
        }
        return true;
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
    
    public function __destruct()
    {
        $this->shutdown();
    }
}
