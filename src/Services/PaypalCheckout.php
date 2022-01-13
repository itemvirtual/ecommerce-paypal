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
    private $total;

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
            "intent" => "AUTHORIZE",
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
     * Setting up request body for Authorize. This can be populated with fields as per need. Refer API docs for more details.
     *
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
        // "Links:";
        // foreach ($response->result->links as $link) {
        //      {$link->rel}
        //      {$link->href}
        //      {$link->method};
        // }

    }

    public function getApprovalLink($response)
    {
        foreach ($response->result->links as $link) {
            if ($link->rel == 'approve') {
                return $link->href;
            }
        }
        return null;
    }

    public function getOrderId($response)
    {
        if ($response->result && $response->result->id) {
            return $response->result->id;
        }
        return null;
    }

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



    // *********************************************************************************** SETTERS


    /**
     * @return mixed
     */
    public function getTotal()
    {
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
