<?php

namespace App\Api\Repositories\Eloquent;

use App\Api\Criteria\ShiftCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\ShiftRepository;
use App\Api\Entities\Shift;
use App\Api\Validators\ShiftValidator;

/**
 * Class ShiftRepositoryEloquent
 */
class ShiftRepositoryEloquent extends BaseRepository implements ShiftRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Shift::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    public function getShift($params = [],$limit = 0) {
        $this->pushCriteria(new ShiftCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new ShiftCriteria($params));
        return $item;
    }
}
