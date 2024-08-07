<?php

namespace NadzorServera\Skijasi\Module\Commerce\Models;

use Illuminate\Database\Eloquent\Model;
use NadzorServera\Skijasi\Models\User;

class Cart extends Model
{
    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = $prefix.'carts';
        parent::__construct($attributes);
    }

    protected $fillable = [
        'id',
        'product_detail_id',
        'user_id',
        'quantity',
        'cekapotvrdu',
        'created_at',
        'updated_at',
        'tblpaymentsid',
    ];

    /**
     * Get the productDetail that owns the Cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    /**
     * Get the user that owns the Cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
