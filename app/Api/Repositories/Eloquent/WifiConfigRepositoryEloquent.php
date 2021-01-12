<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\WifiConfigRepository;
use App\Api\Entities\WifiConfig;
use App\Api\Validators\WifiConfigValidator;

/**
 * Class WifiConfigRepositoryEloquent
 */
class WifiConfigRepositoryEloquent extends BaseRepository implements WifiConfigRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WifiConfig::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
