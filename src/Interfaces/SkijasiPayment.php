<?php

namespace NadzorServera\Skijasi\Module\Commerce\Interfaces;

interface SkijasiPayment
{
    /**
     * Get all payment slugs.
     *
     * @return array List of all payment options
     */
    public function getPaymentSlug();

    /**
     * Get all protected payment slugs.
     *
     * @return array List of all protected payment options
     */
    public function getProtectedPaymentSlug();
}
