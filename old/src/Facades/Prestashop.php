<?php

namespace Nextpointer\Prestashop\Facades;

use Illuminate\Support\Facades\Facade;

class Prestashop extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'prestashop';
    }
}