<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\PositionTransformer;
use Moloquent\Eloquent\SoftDeletes;

class Position extends Moloquent
{
	use SoftDeletes;

	protected $collection = 'positions';

    protected $guarded = array();

    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transform()
    {
        $transformer = new PositionTransformer();

        return $transformer->transform($this);
    }
    public function shop()
    {
        $shop = null;
        if(!empty($this->shop_id)) {
            $shop = Shop::where(['_id' => mongo_id($this->shop_id)])->first();
        }
        return $shop;
    }

}
