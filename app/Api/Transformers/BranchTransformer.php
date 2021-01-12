<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Branch;

/**
 * Class BranchTransformer
 */
class BranchTransformer extends TransformerAbstract
{

    /**
     * Transform the \Branch entity
     * @param \Branch $model
     *
     * @return array
     */
    // public function transform(Branch $model, string $type = ''){
    //     $data = [
    //         'id'=>$model->_id,
    //         'branch_name' => $model->branch_name,
    //         'address' => $model->address,
    //         'shop'=>[],
    //     ];
    //     dd($data);
    //     $shop=$model->shop();
    //     if(!empty($shop))
    //     {
    //         $data['shop']=$shop->transform();
    //     }
    //     return $data;
    // }
    public function transform(Branch $model, string $type = ''){
        $data = [
            'id'=>$model->_id,
            'name' => $model->name,
            'address' => $model->address,
            'note'=>$model->note,
            'shop'=>[],
        ];
        // dd($data);
        $shop=$model->shop();
        if(!empty($shop))
        {
            $data['shop']=$shop->transform();
        }
        return $data;
    }

}
