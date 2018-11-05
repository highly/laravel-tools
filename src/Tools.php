<?php

namespace Aixue\Tools;

use Aixue\Tools\Services\RabbitMQ\MqHandler;
use Aixue\Tools\Services\Guzzle\GuzzleHandler;
use Illuminate\Container\Container;

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
     * @param GuzzleHandler $guzzleHandler
     */
    public function __construct(
        GuzzleHandler $guzzleHandler
    ) {
        $this->guzzleHandler = $guzzleHandler;
    }
    
    /**
     * @param        $exchange_group_name
     * @param string $connection
     * @return mixed
     */
    public function rabbitMQ($exchange_group_name, $connection = MqHandler::CONNECTION_NAME_DEFAULT)
    {
        return Container::getInstance()
                        ->make(MqHandler::class)
                        ->setConnection($connection)
                        ->setExchangeGroup($exchange_group_name);
    }
    
    /**
     * @param bool $init_options
     * @return GuzzleHandler
     */
    public function request($init_options = true)
    {
        if ($init_options) {
            return $this->guzzleHandler->init();
        }
        return $this->guzzleHandler;
    }
}