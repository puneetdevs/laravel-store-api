<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $table = 'products';

    public $fillable = [
        'title',
        'user_id',
        'description',
        'gross_price',
        'net_price',
        'vat',
        'shipping_cost',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'price' => 'integer',
        'gross_price' => 'integer',
        'net_price' => 'integer',
        'vat' => 'integer',
        'shipping_cost' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required',
        'user_id' => 'nullable',
        'title' => 'required',
        'description' => 'required',
        'price' => 'required',
        'gross_price' => 'nullable',
        'net_price' => 'nullable',
        'vat' => 'nullable',
        'shipping_cost' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
