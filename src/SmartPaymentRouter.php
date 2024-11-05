<?php

namespace Otolorinrufus\SmartPaymentRouter;

use Otolorinrufus\SmartPaymentRouter\Contracts\PaymentGatewayContract;
use Otolorinrufus\SmartPaymentRouter\Traits\PaymentGatewayTrait;

class SmartPaymentRouter
{
    use PaymentGatewayTrait;

    protected $gateways = [];

    public function __construct()
    {
        $this->gateways = config('smartpaymentrouter.gateways');
    }

    /**
     * Routes the payment to the best available payment gateway.
     *
     * @param array $formData
     * @return string|array
     */
    public function routePayment($formData)
    {
        // Use the trait to process payment
        $bestGateway = $this->selectBestGateway($formData);
        
        if (!$bestGateway) {
            return 'No suitable payment gateway found.';
        }

        return [
            'gateway' => $bestGateway['name'],
            'message' => 'Payment has been routed to the best gateway'. $bestGateway['name'],
        ];
    }
}
