<?php

namespace App\Api\Repositories\Eloquent;

use App\Api\Criteria\EmpshiftCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\EmpshiftRepository;
use App\Api\Entities\Empshift;
use App\Api\Validators\EmpshiftValidator;

/**
 * Class EmpshiftRepositoryEloquent
 */
class EmpshiftRepositoryEloquent extends BaseRepository implements EmpshiftRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Empshift::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    public function getEmpshift($params = [],$limit = 0) {
        $this->pushCriteria(new EmpshiftCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new EmpshiftCriteria($params));
        return $item;
    }
}
