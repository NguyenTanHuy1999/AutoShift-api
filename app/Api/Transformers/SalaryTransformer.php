<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Salary;

/**
 * Class SalaryTransformer
 */
class SalaryTransformer extends TransformerAbstract
{

    /**
     * Transform the \Salary entity
     * @param \Salary $model
     *
     * @return array
     */
    public function transform(Salary $model)
    {

        $id=$model->user_id;
        return [
            'user_id'=> $model->user_id,
            'work_time'=>$model->work_time,
            'salary'=>$model->salary,
        ];
    }
}
