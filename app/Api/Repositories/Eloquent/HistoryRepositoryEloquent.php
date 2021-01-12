<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\HistoryRepository;
use App\Api\Entities\History;
use App\Api\Validators\HistoryValidator;

/**
 * Class HistoryRepositoryEloquent
 */
class HistoryRepositoryEloquent extends BaseRepository implements HistoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return History::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    public function getHistory($params = [],$limit = 0) {
        $this->pushCriteria(new HistoryCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new HistoryCriteria($params));
        return $item;
    }
}
