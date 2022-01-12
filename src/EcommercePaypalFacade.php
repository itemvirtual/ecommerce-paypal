<?php

namespace Itemvirtual\EcommercePaypal;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Itemvirtual\EcommercePaypal\Skeleton\SkeletonClass
 */
class EcommercePaypalFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ecommerce-paypal';
    }
}
