<?php

namespace NadzorServera\Skijasi\Module\Commerce\Facades;

use Illuminate\Support\Facades\Facade;

class SkijasiCommerceModule extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'skijasi-commerce-module';
    }
}
