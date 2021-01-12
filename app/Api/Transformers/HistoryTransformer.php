<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\History;

/**
 * Class HistoryTransformer
 */
class HistoryTransformer extends TransformerAbstract
{

    /**
     * Transform the \History entity
     * @param \History $model
     *
     * @return array
     */
    public function transform(History $model)
    {
        $s_name = $model->shift_name;
        $s_time = $model->shift_time;
        $status = $model->status;
        $mean = "";
        if ($status == 1) {
            $mean = "Vào ca";
        } elseif ($status == 0) {
            $mean = "Ra ca";
        }
        $shift_status = $mean;

        $shift = $model->shift();
        return [
            'date' => format_get_date($model->time_check, 'date'),
            'data' => [
                'name' => $model->user_name,
                'time' => format_get_date($model->time_check),
                'activity' => $shift_status,
                'shift' => $shift ? $shift->transform() : null
            ],
        ];
    }
}
