<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\TimekeepConfigRepository;
use App\Api\Entities\TimekeepConfig;
use App\Api\Validators\TimekeepConfigValidator;

/**
 * Class TimekeepConfigRepositoryEloquent
 */
class TimekeepConfigRepositoryEloquent extends BaseRepository implements TimekeepConfigRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TimekeepConfig::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
