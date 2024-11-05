<?php

namespace Otolorinrufus\SmartPaymentRouter\Contracts;

interface PaymentGatewayContract
{
    public function getReliabilityStatus();
    public function getSupportedCurrencies();
    public function getTransactionCharges();
}
