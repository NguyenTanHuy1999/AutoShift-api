<?php

namespace App\Api\Repositories\Eloquent;

use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\PositionRepository;
use App\Api\Repositories\Contracts\BranchRepository;
use App\Api\Repositories\Contracts\DepRepository;
use App\Api\Repositories\Contracts\ShopRepository;
use App\Api\Entities\Shop;
use App\Api\Entities\User;
use App\Api\Entities\Position;
use App\Api\Entities\Branch;
use App\Api\Entities\Dep;
use App\Api\Validators\ShopValidator;
use App\Api\Criteria\ShopCriteria;
use App\Api\Entities\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
/**
 * Class ShopRepositoryEloquent
 */
class ShopRepositoryEloquent extends BaseRepository implements ShopRepository
{
    protected $userRepository;
    protected $depRepository;
    protected $branchRepository;
    protected $positionRepository;
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Shop::class;
    }

    public function __construct(Application $app,UserRepository $userRepository,
                                PositionRepository $positionRepository,
                                DepRepository $depRepository,
                                BranchRepository $branchRepository)
    {
        $this->userRepository=$userRepository;
        $this->depRepository=$depRepository;
        $this->branchRepository=$branchRepository;
        $this->positionRepository=$positionRepository;
        parent::__construct($app);
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {

    }
    /**
    * Get list Shop
    **/
    public function getShop($params = [],$limit = 0) {
        $this->pushCriteria(new ShopCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();  
        } else {
            $item = $this->all(); 
        }
        $this->popCriteria(new ShopCriteria($params));
        return $item;
    }
    public function deleteShop($id,$limit =0)
    {
            if(is_array($id))
            {
                foreach($id as $row)
                {
                    $user=User::where('shop_id',mongo_id($row->id))->delete();
                    $position=Position::where('shop_id',mongo_id($row->id))->delete();
                    $dep=Dep::where('shop_id',mongo_id($row->id))->delete();
                    $branch=Branch::where('shop_id',mongo_id($row->id))->delete();
                    $shop=Shop::where('_id',mongo_id($row->id))->delete();
                }

            }
            else
            {
                $user=User::where('shop_id',mongo_id($id))->delete();
                $position=Position::where('shop_id',mongo_id($id))->delete();
                $dep=Dep::where('shop_id',mongo_id($id))->delete();
                $branch=Branch::where('shop_id',mongo_id($id))->delete();
                $shop=Shop::where('_id',mongo_id($id))->delete();
            }
        return;
    }
}
