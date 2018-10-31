<?php

namespace Aixue\Tools\Facades;

use Aixue\Tools\MQ;
use Illuminate\Support\Facades\Facade;

class ToolsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MQ::class;
    }
}

