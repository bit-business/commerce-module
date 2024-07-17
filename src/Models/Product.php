<?php

namespace NadzorServera\Skijasi\Module\Commerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NadzorServera\Skijasi\Module\Commerce\Factories\ProductFactory;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = $prefix.'products';
        parent::__construct($attributes);
    }

    protected $fillable = [
        'id',
        'product_category_id',
        'name',
        'slug',
        'desc',
        'desc2',
        'desc3',
        'desc4',
        'desc5',
        'product_image',

        'datum_pocetka',
        'datum_kraja',
        'mjesto',
        'prijavnicalink',
        'form_id',
        'galleryimages',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the productCategory that owns the Product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Get all of the productDetails for the Product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productDetails()
    {
        return $this->hasMany(ProductDetail::class);
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    /**
     * Get all of the review for the Product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function review()
    {
        return $this->hasMany(ProductReview::class);
    }
}
