<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Position;

/**
 * Class PositionTransformer
 */
class PositionTransformer extends TransformerAbstract
{

    /**
     * Transform the \Position entity
     * @param \Position $model
     *
     * @return array
     */
    public function transform(Position $model)
    {
        $data= [
            'id'         => $model->_id,
            'position_name'  =>$model->position_name,
            'permission'=>$model->permission,
            'shop'=>[],
        ];
        $shop=$model->shop();
        if(!empty($shop))
        {
            $data['shop']=$shop;
        }
        return $data;
    }
}
