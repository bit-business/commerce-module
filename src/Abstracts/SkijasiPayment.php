<?php

namespace NadzorServera\Skijasi\Module\Commerce\Abstracts;

abstract class SkijasiPayment
{
    /**
     * Set all payment options that available for your payment
     * options.
     *
     * @return array An array that filled with classes
     */
    protected $payment_slug;

    /**
     * Set protected payment options to hide it from
     * Payments menu. Instead create your own configuration
     * menu & logic there.
     *
     * @return array An array that filled with classes
     */
    protected $protected_payment_slug;
}
