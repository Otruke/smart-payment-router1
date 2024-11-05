# Smart Payment Router

**Smart Payment Router** is a versatile Laravel package designed to streamline and route payment requests across multiple payment gateways. This package provides an easy-to-use, extensible system for handling payments, ensuring smooth transactions for your application.

SmartPaymentRouter is a PHP package for intelligently selecting the best payment gateway based on reliability, currency support, and transaction charges. It allows developers to easily integrate multiple payment gateways and select the most optimal one for each transaction.

## Features

- **Multi-gateway support**: Choose the best gateway from a list of supported gateways.
- **Reliability check**: Automatically determine which gateways are reliable.
- **Currency support**: Ensure the selected gateway supports the required currency.
- **Lowest transaction charges**: Select the gateway with the lowest fees.
- **Mock API**: Use mock API responses during testing, with easy configuration via `.env`.
- **Flexible configuration**: Customize gateways via the configuration file.

## Installation

    To install the package, you need to add the package to your Laravel project.

1. **Via Composer**:

    Run the following command in your Laravel project directory:

    ```bash
    composer require otolorinrufus/smart-payment-router
    ```

2. **Publish Configurations**:

    After installation, publish the configuration file using:

    ```bash
    php artisan vendor:publish --provider="Otolorinrufus\SmartPaymentRouter\SmartPaymentRouterServiceProvider" --tag="config"

    ```

3. **Update .env File**:

    To configure the available payment gateways, modify the .env file to the nth number of payment gateway to be used. see example below:

    ```bash
    SMARTPAY_USE_MOCK=true   # Set to true for mock data, false for real API calls

    SMARTPAY_GATEWAYS_COUNT=3 # Set nth number of payment gateway you are integrating


    SMARTPAY1_PAYMENTGATEWAYNAME=gateway1
    SMARTPAY1_PAYMENTGATEWAYNAME_RELIABILITYURL=http://gateway1.com/api/reliability
    SMARTPAY1_PAYMENTGATEWAYNAME_CURRENCYURL=http://gateway1.com/api/currencies
    SMARTPAY1_PAYMENTGATEWAYNAME_CHARGESURL=http://gateway1.com/api/charges

    SMARTPAY2_PAYMENTGATEWAYNAME=gateway2
    SMARTPAY2_PAYMENTGATEWAYNAME_RELIABILITYURL=http://gateway12.com/api/reliability
    SMARTPAY2_PAYMENTGATEWAYNAME_CURRENCYURL=http://gateway2.com/api/currencies
    SMARTPAY2_PAYMENTGATEWAYNAME_CHARGESURL=http://gateway2.com/api/charges

    SMARTPAY3_PAYMENTGATEWAYNAME=gateway3
    SMARTPAY3_PAYMENTGATEWAYNAME_RELIABILITYURL=http://gateway3.com/api/reliability
    SMARTPAY3_PAYMENTGATEWAYNAME_CURRENCYURL=http://gateway3.com/api/currencies
    SMARTPAY3_PAYMENTGATEWAYNAME_CHARGESURL=http://gateway3.com/api/charges
    ```
    When SMARTPAY_USE_MOCK is set to true, the package will simulate API responses using mock data. Set it to false to perform actual API calls.


## Usage

**Setting up Payment Gateways**:

    Here’s how you can use the package in your Laravel application or PHP project:

- Inject or instantiate the PaymentGatewayHandler class.
- Call the selectBestGateway method, passing the required form data.

    ```php
    use Otolorinrufus\SmartPaymentRouter\Gateways\PaymentGatewayHandler;

    class PaymentController extends Controller
    {
        protected $paymentGatewayHandler;

        public function __construct(PaymentGatewayHandler $paymentGatewayHandler)
        {
            $this->paymentGatewayHandler = $paymentGatewayHandler;
        }

        public function processPayment(Request $request)
        {
            $formData = [
                'currency' => $request->input('currency'),
            ];

            $bestGateway = $this->paymentGatewayHandler->selectBestGateway($formData);

            if ($bestGateway) {
                // Proceed with the selected gateway
                return response()->json([
                    'message' => 'Payment processed using ' . $bestGateway['name'],
                ]);
            }

            return response()->json(['error' => 'No suitable payment gateway found.'], 400);
        }
    }
    ```

## Handling API Calls
    The PaymentGatewayHandler uses the Laravel HTTP client to make real API calls. It checks:
- **Reliability**: Checks whether the gateway is reliable by making an API call to the reliability_url.
- **Currency Support**: Verifies if the gateway supports the currency requested by making an API call to the    currency_url.
- **Transaction Charges**: Determines the gateway with the lowest transaction fees by calling the charges_url.

    You can configure these URLs in the .env configuration file.

   
## Using Mock Data

    During testing, you may not want to hit live APIs. To enable mock responses for API calls, simply set the SMARTPAY_USE_MOCK environment variable to true.
    The package will return predefined mock responses for reliability, currency support, and transaction charges when mock mode is enabled.

    ```php
    protected function mockApiResponse($url)
    {
        if (strpos($url, 'reliability') !== false) {
            return ['status' => 'reliable'];
        }

        if (strpos($url, 'currencies') !== false) {
            return ['supported_currencies' => ['USD', 'EUR']];
        }

        if (strpos($url, 'charges') !== false) {
            return ['charges' => 2];
        }

        return null;
    }

    ```

## License

    This package is open-sourced software licensed under the MIT license.

## Contribution

    Contributions to the Smart Payment Router package are welcome! Please submit a pull request or open an issue on GitHub.


