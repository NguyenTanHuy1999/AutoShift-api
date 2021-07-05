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
            'user_info'=> $model->user_info,
            'total_work_time'=>$model->total_work_time,
            'total_work_day'=>$model->total_work_day,
            'total_late_check_in'=>$model->total_late_check_in,
            'total_soon_check_out'=>$model->total_soon_check_out,
            'month'=>$model->month,
            'year' =>$model->year,
            'real_salary'=>$model->real_salary
        ];
    }
}
