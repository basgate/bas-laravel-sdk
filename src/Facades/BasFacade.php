<?php

namespace Bas\LaravelSdk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bas\LaravelSdk\Services\BasService
 */
class BasFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bas';
    }
}
