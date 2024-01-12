<?php

namespace Kima92\ExpectorPatronum\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kima92\ExpectorPatronum\ExpectorPatronum
 */
class ExpectorPatronum extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kima92\ExpectorPatronum\ExpectorPatronum::class;
    }
}
