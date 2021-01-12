<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\BranchTransformer;
use Moloquent\Eloquent\SoftDeletes;

class Branch extends Moloquent
{
	use SoftDeletes;

	protected $collection = 'branches';

    protected $guarded = array();

    protected $hidden = ['updated_at','deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'start_package',
        'end_package',
        'last_activity'
    ];

    public function transform(string $type = '')
    {
        $transformer = new BranchTransformer();

        return $transformer->transform($this, $type);
    }

    public function transformSelect()
    {
        $transformer = new BranchTransformer();

        return $transformer->transformSelect($this);
    }
    // public function shop() {
    //     $shop = null;
    //     if(!empty($this->shop_id)) {
    //         $shop = Shop::where(['_id' => mongo_id($this->shop_id)])->first();
    //     }
    //     return $shop;
    // }
    public function shop() {
        $shop = null;
        if(!empty($this->shop_id)) {
            $shop = Shop::where(['_id' => mongo_id($this->shop_id)])->first();
        }
        return $shop;
    }


}
