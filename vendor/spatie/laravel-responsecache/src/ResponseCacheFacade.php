<?php

namespace Spatie\ResponseCache;

use Illuminate\Support\Facades\Facade;

class ResponseCacheFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @see \Spatie\ResponseCache\ResponseCache
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'responsecache';
    }
}
