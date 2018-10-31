<?php

namespace Aixue\Tools\Facades;

use Aixue\Tools\Tools;
use Illuminate\Support\Facades\Facade;

class ToolsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Tools::class;
    }
}
