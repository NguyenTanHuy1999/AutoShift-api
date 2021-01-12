<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\ShopTransformer;
use Moloquent\Eloquent\SoftDeletes;

use App\Api\Entities\UserProvince;
use App\Api\Entities\UserTown;

class Shop extends Moloquent
{
	use SoftDeletes;

	protected $collection = 'shops';

    /**
     * To make all fields fillable.
     */
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
        $transformer = new ShopTransformer();

        return $transformer->transform($this, $type);
    }

    public function transformSelect()
    {
        $transformer = new ShopTransformer();

        return $transformer->transformSelect($this);
    }

    

    public function province()
    {
        $province =  UserProvince::where(['_id' => $this->province_id])->first();
        if(!empty($province)) {
            return $province->transform();
        } else {
            return [];
        }
    }

    public function town()
    {
        $town =  UserTown::where(['_id' => $this->town_id])->first();
        if(!empty($town)) {
            return $town->transform();
        } else {
            return [];
        }
    }
    public function user()
    {
        $user=User::where(['_id'=>$this->_id])->first();
        if(!empty($user)) {
            return $user->transform();
        } else {
            return [];
        }
    }
}
