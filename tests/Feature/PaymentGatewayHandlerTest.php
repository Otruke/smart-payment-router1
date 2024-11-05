<?php

namespace Tests\Feature;

use Otolorinrufus\SmartPaymentRouter\Gateways\PaymentGatewayHandler;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Http;

class PaymentGatewayHandlerTest extends TestCase
{
    protected $handler;

    protected function setUp(): void
    {
        parent::setUp();

        // Instantiate the handler with mock data
        $this->handler = new PaymentGatewayHandler();

        // Mock the environment variable for using mock data
        putenv('SMARTPAY_USE_MOCK=true');
    }

    public function testSelectBestGatewayReturnsGatewayWhenReliableAndSupportsCurrency()
    {
        // Mock HTTP responses using Laravel's Http facade
        Http::fake([
            'http://gateway1.com/api/reliability' => Http::response(['status' => 'reliable'], 200),
            'http://gateway1.com/api/currencies' => Http::response(['supported_currencies' => ['USD']], 200),
            'http://gateway1.com/api/charges' => Http::response(['charges' => 2], 200),
        ]);

        // Mock the form data
        $formData = ['currency' => 'USD'];

        // Call the selectBestGateway method
        $bestGateway = $this->handler->selectBestGateway($formData);

        // Assert that the correct gateway is returned
        $this->assertNotNull($bestGateway);
        $this->assertEquals('Gateway1', $bestGateway['name']);
    }

    public function testSelectBestGatewayReturnsNullWhenNoReliableGateways()
    {
        // Simulate unreliable gateway
        Http::fake([
            'http://gateway1.com/api/reliability' => Http::response(['status' => 'unreliable'], 200),
        ]);

        // Mock the form data
        $formData = ['currency' => 'USD'];

        // Call the selectBestGateway method
        $bestGateway = $this->handler->selectBestGateway($formData);

        // Assert that null is returned when no reliable gateways are found
        $this->assertNull($bestGateway);
    }

    public function testSelectBestGatewayHandlesCurrencySupportCorrectly()
    {
        // Simulate a reliable gateway that does not support the required currency
        Http::fake([
            'http://gateway1.com/api/reliability' => Http::response(['status' => 'reliable'], 200),
            'http://gateway1.com/api/currencies' => Http::response(['supported_currencies' => ['EUR']], 200),
        ]);

        // Mock the form data with a currency not supported by the gateway
        $formData = ['currency' => 'USD'];

        // Call the selectBestGateway method
        $bestGateway = $this->handler->selectBestGateway($formData);

        // Assert that null is returned when no gateways support the currency
        $this->assertNull($bestGateway);
    }

    public function testSelectBestGatewaySelectsLowestCharges()
    {
        // Simulate multiple reliable gateways with different charges
        Http::fake([
            'http://gateway1.com/api/reliability' => Http::response(['status' => 'reliable'], 200),
            'http://gateway2.com/api/reliability' => Http::response(['status' => 'reliable'], 200),
            'http://gateway1.com/api/currencies' => Http::response(['supported_currencies' => ['USD']], 200),
            'http://gateway2.com/api/currencies' => Http::response(['supported_currencies' => ['USD']], 200),
            'http://gateway1.com/api/charges' => Http::response(['charges' => 3], 200),
            'http://gateway2.com/api/charges' => Http::response(['charges' => 1], 200),
        ]);

        // Mock the form data
        $formData = ['currency' => 'USD'];

        // Call the selectBestGateway method
        $bestGateway = $this->handler->selectBestGateway($formData);

        // Assert that the gateway with the lowest charges is selected
        $this->assertNotNull($bestGateway);
        $this->assertEquals('Gateway2', $bestGateway['name']);
    }
}
