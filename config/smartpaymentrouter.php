<?php

$gatewaysCount = env('SMARTPAY_GATEWAYS_COUNT', 2); // Default to 2 if not set
$gateways = [];

for ($i = 1; $i <= $gatewaysCount; $i++) {
    $gateways[] = [
        'name' => env("SMARTPAY{$i}_PAYMENTGATEWAYNAME", "Gateway{$i}"),
        'reliability_url' => env("SMARTPAY{$i}_PAYMENTGATEWAYNAME_RELIABILITYURL", "https://gateway{$i}.com/api/reliability"),
        'currency_url' => env("SMARTPAY{$i}_PAYMENTGATEWAYNAME_CURRENCYURL", "https://gateway{$i}.com/api/currencies"),
        'charges_url' => env("SMARTPAY{$i}_PAYMENTGATEWAYNAME_CHARGESURL", "https://gateway{$i}.com/api/charges"),
    ];
}

return [
    'gateways' => $gateways,
];
