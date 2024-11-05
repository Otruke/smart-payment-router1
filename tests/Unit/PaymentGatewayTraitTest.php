<?php

namespace Tests\Unit;

use Otolorinrufus\SmartPaymentRouter\Traits\PaymentGatewayTrait;
use PHPUnit\Framework\TestCase;

class PaymentGatewayTraitTest extends TestCase
{
    use PaymentGatewayTrait;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the gateways for testing
        $this->gateways = [
            [
                'name' => 'Gateway1',
                'reliability_url' => 'http://gateway1.com/api/reliability',
                'currency_url' => 'http://gateway1.com/api/currencies',
                'charges_url' => 'http://gateway1.com/api/charges',
            ],
            // Add more mock gateways as needed
        ];
    }

    public function testSelectBestGatewayReturnsGatewayWhenReliableAndSupportsCurrency()
    {
        // Mock response for the reliability check
        $this->method('callApi')
            ->willReturnOnConsecutiveCalls(
                ['status' => 'reliable'], // For reliability check
                ['supported_currencies' => ['USD']], // For currency check
                ['charges' => 3] // For charges check
            );

        $formData = ['currency' => 'USD'];
        $bestGateway = $this->selectBestGateway($formData);

        $this->assertNotNull($bestGateway);
        $this->assertEquals('Gateway1', $bestGateway['name']);
    }

    public function testSelectBestGatewayReturnsNullWhenNoReliableGateway()
    {
        $this->method('callApi')
            ->willReturn(['status' => 'unreliable']); // Simulating an unreliable gateway

        $formData = ['currency' => 'USD'];
        $bestGateway = $this->selectBestGateway($formData);

        $this->assertNull($bestGateway);
    }

   
}
