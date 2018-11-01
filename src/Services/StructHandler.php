<?php

namespace Aixue\Tools\Services;

use Aixue\Tools\Traits\HandlerTrait;

/**
 * Class StructHandler
 */
class StructHandler
{
    use HandlerTrait;
    
    protected function noLimit()
    {
        set_time_limit(0);
        return $this;
    }
}
