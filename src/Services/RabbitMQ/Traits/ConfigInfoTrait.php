<?php

namespace Aixue\Tools\Services\RabbitMQ\Traits;

use Aixue\Tools\Exceptions\RabbitMqException;

trait ConfigInfoTrait
{
    /**
     * @var string
     */
    protected $mq_host;
    
    /**
     * @var int
     */
    protected $mq_port = 5672;
    
    /**
     * @var string
     */
    protected $mq_user;
    
    /**
     * @var string
     */
    protected $mq_pwd = '';
    
    /**
     * @var string
     */
    protected $mq_vhost = '/';
    
    /**
     * @var string
     */
    protected $exchange_name;
    
    /**
     * @var string
     */
    protected $queue_name;
    
    /**
     * @var string
     */
    protected $routing_key = '';
    
    /**
     * @var string
     */
    protected $consumer_tag = '';
    
    /**
     * @var string
     */
    protected $exchange_type = 'direct';
    
    /**
     * @var bool
     */
    protected $passive = false;
    
    /**
     * @var bool
     */
    protected $durable = true;
    
    /**
     * @var bool
     */
    protected $auto_delete = false;
    
    /**
     * @var bool
     */
    protected $exclusive = false;
    
    /**
     * @var bool
     */
    protected $consume_no_local = false;
    
    /**
     * @var bool
     */
    protected $consume_no_ack = false;
    
    /**
     * @var bool
     */
    protected $consume_no_wait = false;
    
    /**
     * @return string
     */
    public function getMqHost()
    {
        return $this->mq_host;
    }
    
    /**
     * @return int
     */
    public function getMqPort()
    {
        return $this->mq_port;
    }
    
    /**
     * @return string
     */
    public function getMqUser()
    {
        return $this->mq_user;
    }
    
    /**
     * @return string
     */
    public function getMqPwd()
    {
        return $this->mq_pwd;
    }
    
    /**
     * @return string
     */
    public function getMqVhost()
    {
        return $this->mq_vhost;
    }
    
    /**
     * @return string
     */
    public function getExchange()
    {
        return $this->exchange_name;
    }
    
    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue_name;
    }
    
    /**
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routing_key;
    }
    
    /**
     * @param null $consumer_tag
     * @return null|string
     */
    public function getConsumerTag($consumer_tag = null)
    {
        return $consumer_tag === null ? $this->consumer_tag : $consumer_tag;
    }
    
    /**
     * @param $type
     * @return $this
     */
    public function setExchangeType($type)
    {
        $this->exchange_type = (string) $type;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getExchangeTypeCfg()
    {
        return $this->exchange_type;
    }
    
    /**
     * @param $bool
     * @return $this
     */
    public function setPassive($bool)
    {
        $this->passive = (bool) $bool;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getPassiveCfg()
    {
        return $this->passive;
    }
    
    /**
     * @param $bool
     * @return $this
     */
    public function setDurable($bool)
    {
        $this->durable = (bool) $bool;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getDurableCfg()
    {
        return $this->durable;
    }
    
    /**
     * @param $bool
     * @return $this
     */
    public function setAutoDelete($bool)
    {
        $this->auto_delete = (bool) $bool;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getAutoDeleteCfg()
    {
        return $this->auto_delete;
    }
    
    /**
     * @param $bool
     * @return $this
     */
    public function setExclusive($bool)
    {
        $this->exclusive = (bool) $bool;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getExclusiveCfg()
    {
        return $this->exclusive;
    }
    
    /**
     * @param $bool
     * @return $this
     */
    public function setConsumeNoLocal($bool)
    {
        $this->consume_no_local = (bool) $bool;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getConsumeNoLocalCfg()
    {
        return $this->consume_no_local;
    }
    
    /**
     * @param $bool
     * @return $this
     */
    public function setConsumeNoAck($bool)
    {
        $this->consume_no_ack = (bool) $bool;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getConsumeNoAckCfg()
    {
        return $this->consume_no_ack;
    }
    
    /**
     * @param $bool
     * @return $this
     */
    public function setConsumeNoWait($bool)
    {
        $this->consume_no_wait = (bool) $bool;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getConsumeNoWaitCfg()
    {
        return $this->consume_no_wait;
    }
    
    /**
     * @return array
     */
    public function getCfgInfoForLog()
    {
        return [
            'host'        => $this->getMqHost(),
            'exchange'    => $this->getExchange(),
            'queue'       => $this->getQueue(),
            'routing_key' => $this->getRoutingKey(),
        ];
    }
    
    /**
     * @param $connection_name
     * @return $this
     * @throws RabbitMqException
     */
    public function setConnection($connection_name)
    {
        if (! isset($this->configMap['connections'][$connection_name])) {
            throw new RabbitMqException("connection [{$connection_name}] is not defined.");
        }
        $connection = $this->configMap['connections'][$connection_name];
        
        if (empty($connection['host'])) {
            throw new RabbitMqException('mq host must be defined.');
        }
        $this->mq_host = $connection['host'];
        
        if (! empty($connection['port'])) {
            $this->mq_port = (int) $connection['port'];
        }
        
        if (empty($connection['user'])) {
            throw new RabbitMqException('mq user must be defined.');
        }
        $this->mq_user = $connection['user'];
        
        if (! isset($connection['password'])) {
            throw new RabbitMqException('mq password must be defined.');
        }
        $this->mq_pwd = $connection['password'];
        
        if (! empty($connection['vhost'])) {
            $this->mq_vhost = trim($connection['vhost']);
        }
        
        return $this;
    }
    
    /**
     * @param $exchange_group_name
     * @return $this
     * @throws RabbitMqException
     */
    public function setExchangeGroup($exchange_group_name)
    {
        if (! isset($this->configMap['exchange'][$exchange_group_name])) {
            throw new RabbitMqException("exchange group [{$exchange_group_name}] is not defined.");
        }
        
        $exchange_group = $this->configMap['exchange'][$exchange_group_name];
        
        if (empty($exchange_group['exchange_name'])) {
            throw new RabbitMqException('exchange name must be defined.');
        }
        $this->exchange_name = $exchange_group['exchange_name'];
        
        if (empty($exchange_group['queue_name'])) {
            throw new RabbitMqException('queue name must be defined.');
        }
        $this->queue_name   = $exchange_group['queue_name'];
        
        $this->routing_key  = array_get($exchange_group, 'routing_key', '');
        
        $this->consumer_tag = array_get($exchange_group, 'consumer_tag', '');
        
        return $this;
    }
}