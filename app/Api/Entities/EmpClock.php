<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\EmpClockTransformer;
use Moloquent\Eloquent\SoftDeletes;

class EmpClock extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'employee_clocks';

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $dates = [
        'time_in',
        'time_out',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transform($type = '')
    {
        $transformer = new EmpClockTransformer();

        return $transformer->transform($this, $type);
    }
    public function user()
    {
        $user = null;
        if (!empty($this->user_id)) {
            $user = User::where(['_id' => mongo_id($this->user_id)])->first();
        }
        return $user;
    }
    public function shift()
    {
        $shift = null;
        if (!empty($this->shift_id)) {
            $shift = Shift::where(['_id' => mongo_id($this->shift_id)])->first();
        }
        return $shift;
    }
}
