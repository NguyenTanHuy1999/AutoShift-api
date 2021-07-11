<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\GeneralTransformer;
use Moloquent\Eloquent\SoftDeletes;

class General extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'general';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $guarded = array();

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transform()
    {
        $transformer = new GeneralTransformer();

        return $transformer->transform($this);
    }

}
