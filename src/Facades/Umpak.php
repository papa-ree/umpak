<?php

namespace Bale\Umpak\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bale\Umpak\Umpak
 */
class Umpak extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Bale\Umpak\Umpak::class;
    }
}
