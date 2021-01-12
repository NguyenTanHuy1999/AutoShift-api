<?php

namespace App\Api\Transformers;

use App\Api\Entities\Organization;
use App\Api\Entities\Role;
use League\Fractal\TransformerAbstract;

/**
 * Class RoleTransformer.
 */
class RoleTransformer extends TransformerAbstract
{
    /**
     * Transform the \Role entity.
     *
     * @param \Role $model
     *
     * @return array
     */
    public function transform(Role $model, Organization $organization = null)
    {
        $data = array(
            // 'id' => $model->_id,
            'type' => $model->name,
            'type_name' => $model->display_name,
            // 'type' => $model->type,
            );

        if ($organization) {
            $data['organization'] = $organization->transform();
        }

        return $data;
    }
}
