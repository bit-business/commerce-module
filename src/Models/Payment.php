<?php

namespace NadzorServera\Skijasi\Module\Commerce\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = 'skijasi_payment_types';
        parent::__construct($attributes);
    }

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all of the options for the Payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(PaymentOption::class, 'payment_type_id', 'id');
    }
}
