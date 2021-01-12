<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\EmpshiftTransformer;
use Moloquent\Eloquent\SoftDeletes;
use App\Api\Entities\Shift;

class Empshift extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'employee_shifts';

    protected $guarded = array();

    protected $hidden = ['updated_at', 'deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'working_date',
    ];

    public function transform($type = '')
    {
        $transformer = new EmpshiftTransformer();

        return $transformer->transform($this, $type);
    }

    // public function transformSelect()
    // {
    //     $transformer = new EmpshiftTransformer();

    //     return $transformer->transformSelect($this);
    // }
    public function shift()
    {
        $shift = [];
        if (!empty($this->shift_id)) {
            $shift = Shift::where(['_id' => mongo_id($this->shift_id)])->first();
        }
        return $shift;
    }
}
