<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Empshift;

/**
 * Class EmpshiftTransformer
 */
class EmpshiftTransformer extends TransformerAbstract
{

    /**
     * Transform the \Empshift entity
     * @param \Empshift $model
     *
     * @return array
     */
    public function transform(Empshift $model)
    {
        $data = [
            'date' => format_get_date($model->working_date, 'date'),
            'id' => $model->_id
        ];
        $shift_data = [];
        $shift = $model->shift();
        if (!empty($shift)) {
            $shift_data = $shift->transform();
            $data['data'] = $shift_data;
        }
        return $data;
    }
}
