<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\General;

/**
 * Class GeneralTransformer
 */
class GeneralTransformer    extends TransformerAbstract
{

    /**
     * Transform the \General entity
     * @param \General $model
     *
     * @return array
     */
    public function transform(General $model)
    {
        $data = [
            'statistical_1' => $model->statistical_1,
            'statistical_2'=> $model->statistical_2,
            'statistical_3'=> $model->statistical_3
        ];
        return $data;
    }
}
