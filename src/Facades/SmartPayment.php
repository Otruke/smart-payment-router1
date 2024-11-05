<?php

namespace Otolorinrufus\SmartPaymentRouter\Facades;

use Illuminate\Support\Facades\Facade;

class SmartPayment extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor()
    {
        return 'smartpayment';
    }
}
