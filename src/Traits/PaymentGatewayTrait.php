<?php

namespace Otolorinrufus\SmartPaymentRouter\Traits;
use Illuminate\Support\Facades\Http;

trait PaymentGatewayTrait
{
    /**
     * Select the best payment gateway based on reliability, currency, and charges.
     *
     * @param array $formData
     * @return array|null
     */
    public function selectBestGateway($formData)
    {
        // Check gateway reliability
        $reliableGateways = $this->checkReliability();

        if (empty($reliableGateways)) {
            return null;
        }

        // Check if any gateway supports the requested currency
        $supportedGateways = $this->checkCurrency($reliableGateways, $formData['currency']);

        if (empty($supportedGateways)) {
            return null;
        }

        // Select the gateway with the lowest transaction charges
        return $this->checkTransactionCharges($supportedGateways);
    }

    /**
     * Check the reliability of the gateways by calling the API.
     *
     * @return array
     */
    protected function checkReliability()
    {
        $reliableGateways = [];
        
        foreach ($this->gateways as $gateway) {
            $response = $this->callApi($gateway['reliability_url']);
            
            if ($response && $response['status'] === 'reliable') {
                $reliableGateways[] = $gateway;
            }
        }

        return $reliableGateways;
    }

    /**
     * Check which gateways support the currency.
     *
     * @param array $gateways
     * @param string $currency
     * @return array
     */
    protected function checkCurrency($gateways, $currency)
    {
        $supportedGateways = [];

        foreach ($gateways as $gateway) {
            $response = $this->callApi($gateway['currency_url']);

            if ($response && in_array($currency, $response['supported_currencies'])) {
                $supportedGateways[] = $gateway;
            }
        }

        return $supportedGateways;
    }

    /**
     * Check which gateway has the lowest transaction charges.
     *
     * @param array $gateways
     * @return array|null
     */
    protected function checkTransactionCharges($gateways)
    {
        $lowestCharges = null;
        $bestGateway = null;

        foreach ($gateways as $gateway) {
            $response = $this->callApi($gateway['charges_url']);

            if ($response && (!isset($lowestCharges) || $response['charges'] < $lowestCharges)) {
                $lowestCharges = $response['charges'];
                $bestGateway = $gateway;
            }
        }

        return $bestGateway;
    }

    /**
     * Calls the API or returns mock data based on the environment setting.
     *
     * @param string $url
     * @return array|null
     */
    

     protected function callApi($url)
     {
         // Check if we should use mock data
         if (env('SMARTPAY_USE_MOCK', true)) {
             // Simulate API calls
             return $this->mockApiResponse($url);
         }
     
         // For live API calls, use Laravel's HTTP client
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
      * Simulates an API call to return mock responses based on the URL.
      *
      * @param string $url
      * @return array|null
      */
     protected function mockApiResponse($url)
     {
         // Simulate API responses based on the requested URL
         if (strpos($url, 'reliability') !== false) {
             return ['status' => 'reliable'];  // Mock reliable response
         }
     
         if (strpos($url, 'currency') !== false) {
             return ['supported_currencies' => ['USD', 'EUR']];  // Mock supported currencies
         }
     
         if (strpos($url, 'charges') !== false) {
             return ['charges' => rand(1, 5)];  // Mock random charges
         }
     
         return null;
     }

}
