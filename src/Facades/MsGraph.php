<?php

namespace Invictushub\MsGraph\Facades;

use Illuminate\Support\Facades\Facade;

class MsGraph extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'msgraph';
    }
}
