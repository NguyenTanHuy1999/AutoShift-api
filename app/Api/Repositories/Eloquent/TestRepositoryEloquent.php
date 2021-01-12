<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\testRepository;
use App\Api\Entities\Test;
use App\Api\Validators\TestValidator;

/**
 * Class TestRepositoryEloquent
 */
class TestRepositoryEloquent extends BaseRepository implements TestRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Test::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
