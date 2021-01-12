<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\ShiftWeeksTransformer;
use Moloquent\Eloquent\SoftDeletes;

class ShiftWeeks extends Moloquent
{
	use SoftDeletes;

	protected $collection = 'shift_weeks';

    protected $guard = [];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transform()
    {
        $transformer = new ShiftWeeksTransformer();

        return $transformer->transform($this);
    }

}
