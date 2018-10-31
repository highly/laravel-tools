<?php

namespace Aixue\Tools\Services\RabbitMQ\Traits;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

trait MqStructTrait
{
    /**
     * @var AMQPStreamConnection
     */
    public $connection;
    
    /**
     * @var AMQPChannel
     */
    public $channel;
    
    /**
     * @return bool
     */
    protected function initMqConnection()
    {
        if ($this->connection instanceof AbstractConnection && $this->connection->isConnected()) {
            return true;
        }
        
        try {
            $this->connection = new AMQPStreamConnection(
                $this->getMqHost(), $this->getMqPort(), $this->getMqUser(), $this->getMqPwd()
            );
            $this->channel = $this->connection->channel();
            
            $this->channel->exchange_declare(
                $this->getExchange(),
                $this->getExchangeTypeCfg(),
                $this->getPassiveCfg(),
                $this->getDurableCfg(),
                $this->getAutoDeleteCfg()
            );
            
            $this->channel->queue_declare(
                $this->getQueue(),
                $this->getPassiveCfg(),
                $this->getDurableCfg(),
                $this->getExclusiveCfg(),
                $this->getAutoDeleteCfg()
            );
            
            $this->channel->queue_bind($this->getQueue(), $this->getExchange(), $this->getRoutingKey());
            
            return true;
        } catch (\Exception $e) {
            \Log::warning(
                'mq_connection_initialisation_exception',
                [
                    'msg'   => $e->getMessage(),
                    'trace' => $e->getTrace(),
                    'info'  => $this->getCfgInfoForLog(),
                ]
            );
        }
        return false;
    }
    
    /**
     * @return AMQPStreamConnection
     */
    protected function getConnectionHandler()
    {
        return $this->connection;
    }
    
    /**
     * @return AMQPChannel
     */
    protected function getChannelHandler()
    {
        return $this->channel;
    }
}