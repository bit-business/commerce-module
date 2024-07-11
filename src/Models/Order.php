<?php

namespace NadzorServera\Skijasi\Module\Commerce\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NadzorServera\Skijasi\Models\User;

use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = null;

    public $incrementing = false;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = 'skijasi_orders';
        parent::__construct($attributes);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                do {
                    // Generate a random string
                    $randomString = Str::random(8);
                    // Hash the random string and truncate to 9 characters
                    $shortened = substr(hash('sha256', $randomString), 0, 9);

                    // Check for uniqueness
                    $exists = DB::table($model->getTable())->where('id', $shortened)->exists();
                } while ($exists);

                // Assign the unique shortened ID to the model
                $model->id = $shortened;
            } catch (Exception $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    // ovo je od prije samo uuid veliki za generiranje id ordera
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         try {
    //             $model->id = Str::uuid()->toString();
    //         } catch (Exception $e) {
    //             abort(500, $e->getMessage());
    //         }
    //     });
    // }

    protected $guarded = [];

    /**
     * Get the user that owns the Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the OrderPayment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderPayment()
    {
        return $this->hasOne(OrderPayment::class);
    }

    /**
     * Get all of the orderDetails for the Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    /**
     * Get the orderAddress associated with the Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderAddress()
    {
        return $this->hasOne(OrderAddress::class);
    }
}
