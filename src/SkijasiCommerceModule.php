<?php

namespace NadzorServera\Skijasi\Module\Commerce;

use NadzorServera\Skijasi\Module\Commerce\Abstracts\SkijasiPayment as AbstractsSkijasiPayment;
use NadzorServera\Skijasi\Module\Commerce\Interfaces\SkijasiPayment;

class SkijasiCommerceModule extends AbstractsSkijasiPayment implements SkijasiPayment
{
    protected $protected_tables = [
        'products',
        'product_details',
        'product_categories',
        'discounts',
        'orders',
        'order_details',
        'order_payments',
        'carts',
        'user_addresses',
    ];

    protected $protected_payments = [
        'manual-transfer',
    ];

    public function getPaymentSlug()
    {
        return $this->protected_payments;
    }

    public function getProtectedTables()
    {
        return $this->protected_tables;
    }

    public function getProtectedPaymentSlug()
    {
        return $this->protected_payments;
    }
}
