<?php

namespace Otolorinrufus\SmartPaymentRouter\Gateways;

use Otolorinrufus\SmartPaymentRouter\Contracts\PaymentGatewayContract;
use Illuminate\Support\Facades\Http;

class PaymentGatewayHandler implements PaymentGatewayContract
{
    protected $name;
    protected $reliabilityUrl;
    protected $currencyUrl;
    protected $chargesUrl;
    protected $useMock; // control mock or real mode

    public function __construct($name, $reliabilityUrl, $currencyUrl, $chargesUrl, $useMock = false)
    {
        $this->name = $name;
        $this->reliabilityUrl = $reliabilityUrl;
        $this->currencyUrl = $currencyUrl;
        $this->chargesUrl = $chargesUrl;
        $this->useMock = $useMock; // use mock responses
    }

    public function getReliabilityStatus()
    {
        return $this->callApi($this->reliabilityUrl);
    }

    public function getSupportedCurrencies()
    {
        return $this->callApi($this->currencyUrl);
    }

    public function getTransactionCharges()
    {
        return $this->callApi($this->chargesUrl);
    }

    /**
     * Determines whether to call the real API or return mock data based on $useMock flag.
     *
     * @param string $url
     * @return array|null
     */
    protected function callApi($url)
    {
        // Use mock data for testing if the flag is true
        if (env('SMARTPAY_USE_MOCK', true)) {
            return $this->mockApiResponse($url);
        }

        // Otherwise, perform a real API call
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                return $response->json(); // Return actual response data
            }

            return ['error' => 'Failed to connect to API'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()]; // Handle errors gracefully
        }
    }

    /**
     * Simulates an API call by returning mock responses based on the URL content.
     *
     * @param string $url
     * @return array|null
     */
    protected function mockApiResponse($url)
    {
        // Mock data for reliability
        if (strpos($url, 'reliability') !== false) {
            return ['status' => 'reliable'];
        }

        // Mock data for supported currencies
        if (strpos($url, 'currency') !== false) {
            return ['supported_currencies' => ['USD', 'EUR', 'GBP']];
        }

        // Mock data for transaction charges
        if (strpos($url, 'charges') !== false) {
            return ['charges' => rand(2, 10)];
        }

        return null;
    }
}
