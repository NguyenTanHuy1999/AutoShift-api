<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\WifiConfig;

/**
 * Class WifiConfigTransformer
 */
class WifiConfigTransformer extends TransformerAbstract
{

    /**
     * Transform the \WifiConfig entity
     * @param \WifiConfig $model
     *
     * @return array
     */
    public function transform(WifiConfig $model)
    {
        return [
            'id'    => $model->_id,
            'bssid' => $model->bssid,
            'ssid' => $model->ssid,
            'name' => $model->name,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
