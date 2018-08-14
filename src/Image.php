<?php

namespace Laravelista\Picasso;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'picasso';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['original_image', 'dimension_name', 'optimized_image'];
}