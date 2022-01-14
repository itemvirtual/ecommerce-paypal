<?php

namespace Itemvirtual\EcommercePaypal\Services;

use Illuminate\Support\Facades\Log;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;

class PaypalCheckout
{
    private $client;
    private $total = 0;

    public function __construct()
    {
        if (config('ecommerce-paypal.settings.mode') != 'live') {
            $environment = new SandboxEnvironment(config('ecommerce-paypal.client_id'), config('ecommerce-paypal.secret'));
        } else {
            $environment = new ProductionEnvironment(config('ecommerce-paypal.client_id'), config('ecommerce-paypal.secret'));
        }

        $this->client = new PayPalHttpClient($environment);
    }

    private function buildMinimumRequestBody()
    {
        return [
            "intent" => "CAPTURE",
            "application_context" => [
                'return_url' => config('ecommerce-paypal.return_url'),
                'cancel_url' => config('ecommerce-paypal.cancel_url'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => config('ecommerce-paypal.currency_code'),
                        "value" => $this->getTotal(),
                    ]
                ]
            ]
        ];
    }

    /**
     * Creates an order in PayPal.
     * Returns the order information with an approval link for the user to make the payment
     *
     * @return \PayPalHttp\HttpResponse|null
     * @throws \PayPalHttp\IOException
     */
    public function createOrder()
    {
        $request = new OrdersCreateRequest();
        $request->headers["prefer"] = "return=representation";
        $request->body = $this->buildMinimumRequestBody();

        try {
            return $this->client->execute($request);
        } catch (HttpException $e) {
            Log::channel('ecommerce-paypal')->error($e);
            return null;
        }

        // "Status Code: {$response->statusCode}";
        // "Status: {$response->result->status}";
        // "Order ID: {$response->result->id}";
        // "Intent: {$response->result->intent}";
        // "Links: {$response->result->links}";
        //      {$link->rel}
        //      {$link->href}
        //      {$link->method};
        // }

    }

    /**
     * Returns the order information after the user has made the payment
     *
     * @param $orderId
     * @return \PayPalHttp\HttpResponse|null
     * @throws \PayPalHttp\IOException
     */
    public function captureOrder($orderId)
    {
        $request = new OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');
        try {
            return $this->client->execute($request);
        } catch (HttpException $e) {
            Log::channel('ecommerce-paypal')->error($e);
            return null;
        }
    }

    /**
     * Helper function to retrieve the approval link
     *
     * @param $response
     * @return mixed|null
     */
    public function getApprovalLink($response)
    {
        foreach ($response->result->links as $link) {
            if ($link->rel == 'approve') {
                return $link->href;
            }
        }
        return null;
    }

    /**
     * Helper function to retrieve the order id
     *
     * @param $response
     * @return mixed|null
     */
    public function getOrderId($response)
    {
        if ($response->result && $response->result->id) {
            return $response->result->id;
        }
        return null;
    }

    /**
     * Helper function to validate order status
     *
     * @param $response
     * @return bool|null
     */
    public function isOrderSuccessful($response)
    {
        if ($response->statusCode) {
            return $response->statusCode == 201;
        }
        return null;
    }

    // *********************************************************************************** SETTERS


    /**
     * @return mixed
     * @throws \Exception
     */
    public function getTotal()
    {
        if ($this->total <= 0) {
            throw new \Exception('Incorrect amount for PayPal transaction');
        }
        return $this->total;
    }

    /**
     * @param mixed $total
     * @return PaypalCheckout
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

}
