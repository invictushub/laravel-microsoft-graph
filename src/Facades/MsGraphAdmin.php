<?php

namespace Invictushub\MsGraph\Facades;

use Illuminate\Support\Facades\Facade;

class MsGraphAdmin extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'msgraphadmin';
    }
}
