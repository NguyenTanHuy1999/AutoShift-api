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
        $data = [
            'id'    => $model->_id,
            'bssid' => $model->bssid,
            'ssid' => $model->ssid,
            'name' => $model->name,
            'branch' => [],
            'dep' => []
        ];

        $branch = $model->branch();
        $dep = $model->dep();

        if (!empty($branch)) {
            $data['branch'] = $branch->transform();
        }
        if (!empty($dep)) {
            $data['dep'] = $dep->transform();
        }

        return $data;
    }
}
