<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\shift_weeksRepository;
use App\Api\Entities\ShiftWeeks;
use App\Api\Validators\ShiftWeeksValidator;

/**
 * Class ShiftWeeksRepositoryEloquent
 */
class ShiftWeeksRepositoryEloquent extends BaseRepository implements ShiftWeeksRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ShiftWeeks::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
