<?php

namespace App\Api\Repositories\Eloquent;

use App\Api\Criteria\DepCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\BranchRepository;
use App\Api\Repositories\Contracts\DepRepository;
use App\Api\Entities\Dep;
use App\Api\Validators\BranchValidator;

/**
 * Class BranchRepositoryEloquent
 */
class DepRepositoryEloquent extends BaseRepository implements DepRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Dep::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    public function getDep($params = [],$limit = 0) {
        $this->pushCriteria(new DepCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new DepCriteria($params));
        return $item;
    }

    public function getListDep($params = [],$limit = 0){
        $this->pushCriteria(new DepCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            if($limit != 0) {
                $item = $this->paginate($limit); 
            } else {
                $item = $this->paginate(); 
            }
        } else {
            $item = $this->all(); 
        }   
        $this->popCriteria(new DepCriteria($params));
        return $item;
    }
}
