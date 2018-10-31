<?php

namespace Aixue\Tools;

use Aixue\Tools\Services\RabbitMQ\MqHandler;
use Aixue\Tools\Services\Guzzle\GuzzleHandler;

/**
 * Class Tools
 */
class Tools
{
    /**
     * @var MqHandler
     */
    protected $mqHandler;
    
    /**
     * @var GuzzleHandler
     */
    protected $guzzleHandler;
    
    /**
     * Tools constructor.
     * @param MqHandler     $mqHandler
     * @param GuzzleHandler $guzzleHandler'
     */
    public function __construct(
        MqHandler $mqHandler,
        GuzzleHandler $guzzleHandler
    ) {
        $this->mqHandler     = $mqHandler;
        $this->guzzleHandler = $guzzleHandler;
    }
    
    /**
     * @param string $exchange_group_name
     * @param string $connection
     * @return MqHandler
     * @throws Exceptions\RabbitMqException
     */
    public function rabbitMQ($exchange_group_name, $connection = MqHandler::CONNECTION_NAME_DEFAULT)
    {
        return $this->mqHandler->setConnection($connection)->setExchangeGroup($exchange_group_name);
    }
    
    /**
     * @return GuzzleHandler
     */
    public function guzzle()
    {
        return $this->guzzleHandler;
    }
}

