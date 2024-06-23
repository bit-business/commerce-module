<?php

namespace NadzorServera\Skijasi\Module\Commerce\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = 'skijasi_order_payments';
        parent::__construct($attributes);
    }

    protected $guarded = [];

    /**
     * Get the order that owns the OrderPayment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment_type_options()
    {
        return $this->belongsTo(PaymentOption::class);
    }
}
