<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\ShiftWeeks;

/**
 * Class ShiftWeeksTransformer
 */
class ShiftWeeksTransformer extends TransformerAbstract
{

    /**
     * Transform the \ShiftWeeks entity
     * @param \ShiftWeeks $model
     *
     * @return array
     */
    public function transform(ShiftWeeks $model)
    {
        return [
            'id'         => $model->_id,

            

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
