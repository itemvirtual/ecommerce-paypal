# Ecommerce Paypal
> PayPal's payments with Laravel
> 
[![Latest Version on Packagist](https://img.shields.io/packagist/v/itemvirtual/ecommerce-paypal.svg?style=flat-square)](https://packagist.org/packages/itemvirtual/ecommerce-paypal)
[![Total Downloads](https://img.shields.io/packagist/dt/itemvirtual/ecommerce-paypal.svg?style=flat-square)](https://packagist.org/packages/itemvirtual/ecommerce-paypal)


## Installation

You can install the package via composer:

```bash
composer require itemvirtual/ecommerce-paypal
```
Publish config (with `--force` option to update)
``` bash
php artisan vendor:publish --provider="Itemvirtual\EcommercePaypal\EcommercePaypalServiceProvider" --tag=config
```
Add this environment variable to your `.env`
``` bash
ECOMMERCE_PAYPAL_MODE=sandbox  # sandbox or live
ECOMMERCE_PAYPAL_CLIENT_ID=""
ECOMMERCE_PAYPAL_CLIENT_SECRET=""
ECOMMERCE_PAYPAL_RETURN_URL="${APP_URL}/<paypal-ok-url>"
ECOMMERCE_PAYPAL_CANCEL_URL="${APP_URL}/<paypal-cancel-url>"
```

## Usage

Create a PayPal order  
After creating the order, get the approval link for the user to make the payment
```php
use Itemvirtual\EcommercePaypal\Services\PaypalCheckout;

$EcommercePaypal = new PaypalCheckout();
$EcommercePaypalApprovalLink = null;
        
$EcommercePaypalOrder = $EcommercePaypal->setTotal(<TotalAmountToPay>)->createOrder();

if ($EcommercePaypalOrder) {
    $EcommercePaypalApprovalLink = $EcommercePaypal->getApprovalLink($EcommercePaypalOrder);
    $EcommercePaypalOrderId = $EcommercePaypal->getOrderId($EcommercePaypalOrder);
}
```

After the user has made the payment in PayPal, capture order and get payment details
```php
use Itemvirtual\EcommercePaypal\Services\PaypalCheckout;

$paypalOrderId = $request->get('token', null);

$EcommercePaypal = new PaypalCheckout();
$EcommercePaypalResponse = $EcommercePaypal->captureOrder($paypalOrderId);
$isOrderSuccessful = $EcommercePaypal->isOrderSuccessful($EcommercePaypalResponse);
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Itemvirtual](https://github.com/itemvirtual)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.