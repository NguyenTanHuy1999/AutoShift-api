<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\TimekeepConfigTransformer;
use Moloquent\Eloquent\SoftDeletes;

class TimekeepConfig extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'timekeep_config';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $guarded = array();

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transform()
    {
        $transformer = new TimekeepConfigTransformer();

        return $transformer->transform($this);
    }

}
