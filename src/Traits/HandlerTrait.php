<?php

namespace Aixue\Tools\Traits;

trait HandlerTrait
{
    /**
     * @var string
     */
    protected $serviceName;
    
    /**
     * @var array
     */
    protected $configMap;
    
    /**
     * @param array $configMap
     * @return $this
     */
    public function setConfigMap(array $configMap)
    {
        $this->configMap = $configMap;
        return $this;
    }
}