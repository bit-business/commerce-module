<?php

namespace NadzorServera\Skijasi\Module\Commerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZboroviModel extends Model
{
    use  HasFactory;

    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = 'tbl_dep';
        parent::__construct($attributes);
    }

    protected $fillable = [
        'id',
        'ime',
        'depaddress',
        'depphone',
        'depfax',
        'depgsm',
        'predsjednik',
        'tajnik',
        'depemail',
        'depwww',
        'ziroracun',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get all of the productDetail for the Discount.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
 
}
