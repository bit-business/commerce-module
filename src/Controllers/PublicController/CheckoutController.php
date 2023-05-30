<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers\PublicController;

use Illuminate\Http\Request;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Module\Commerce\Interfaces\SkijasiPayment;

class CheckoutController extends Controller implements SkijasiPayment
{
    public function checkout(Request $request)
    {
    }

    public function pay(Request $request)
    {
    }

    /**
     * Get all payment slugs.
     *
     * @return array List of all payment options
     */
    public function getPaymentSlug()
    {
        return [];
    }

    /**
     * Get all protected payment slugs.
     *
     * @return array List of all protected payment options
     */
    public function getProtectedPaymentSlug()
    {
        return [];
    }
}
