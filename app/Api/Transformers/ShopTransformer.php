<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Shop;
use Carbon\Carbon;

/**
 * Class ShopTransformer
 */
class ShopTransformer extends TransformerAbstract
{

    /**
     * Transform the \Shop entity
     * @param \Shop $model
     *
     * @return array
     */
    public function transform(Shop $model, string $type = ''){
        $data = [
            'id' => $model->_id,
            'shop_name'=>$model->shop_name,
            'name' => $model->name,
        ];
        return $data;
    }
}
