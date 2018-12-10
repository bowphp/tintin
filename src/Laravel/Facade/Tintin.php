<?php

namespace Tintin\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

class Tintin extends Facade
{
	/**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tintin';
    }
}