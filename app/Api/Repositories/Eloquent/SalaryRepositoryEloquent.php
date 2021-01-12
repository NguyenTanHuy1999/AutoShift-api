<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\SalaryRepository;
use App\Api\Entities\Salary;
use App\Api\Validators\SalaryValidator;

/**
 * Class SalaryRepositoryEloquent
 */
class SalaryRepositoryEloquent extends BaseRepository implements SalaryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Salary::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    public function getSalary($params = [],$limit = 0) {
        $this->pushCriteria(new SalaryCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new SalaryCriteria($params));
        return $item;
    }
}
