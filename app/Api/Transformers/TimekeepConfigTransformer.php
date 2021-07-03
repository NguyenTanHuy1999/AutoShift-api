<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\TimekeepConfig;

/**
 * Class TimekeepConfigTransformer
 */
class TimekeepConfigTransformer extends TransformerAbstract
{

    /**
     * Transform the \TimekeepConfig entity
     * @param \TimekeepConfig $model
     *
     * @return array
     */
    public function transform(TimekeepConfig $model)
    {
        $data = [
            'id'    => $model->_id,
            'name' => $model->name,
            'wifi' => $model->wifi,
            'location' => $model->location,
            'imageRequire' => $model->imageRequire,
            'shop_id' =>$model->shop_id
        ];

        return $data;
    }
}
