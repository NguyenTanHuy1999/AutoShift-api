<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\WifiConfigTransformer;
use Moloquent\Eloquent\SoftDeletes;

class WifiConfig extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'wifi_config';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $guarded = array();

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transform()
    {
        $transformer = new WifiConfigTransformer();

        return $transformer->transform($this);
    }
}
