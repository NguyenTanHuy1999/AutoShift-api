<?php

namespace App\Api\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\Api\Entities\Shift;

/**
 * Class ShiftTransformer
 */
class ShiftTransformer extends TransformerAbstract
{

    /**
     * Transform the \Shift entity
     * @param \Shift $model
     *
     * @return array
     */
    public function transform(Shift $model, $type = '')
    {
        //dung de tinh luong
        $time_in = $model->time_begin;
        $time_out = $model->time_end;
        $time = $time_in . '-' . $time_out;

        return [
            'id' => $model->_id,
            'name' => $model->name,
            'key' => $model->shift_key,
            'timeBegin' => $model->time_begin,
            'timeEnd' => $model->time_end,
            'time' => $time,
            'assignments' => $model->assignments,
            'branch_ids' => $model->branch_ids,
            'dep_ids' => $model->dep_ids
        ];
    }
    /*public function transform(Shift $model)
    {
        return [
                'name'=>$model->shift_name,
                'time'=>$model->time,
        ];


    }*/
}
