<?php

namespace App\Greeting;

use Illuminate\Support\Facades\Facade;

class GreetingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'greeting';
    }
}
