<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\GeneralRepository;
use App\Api\Entities\General;
use App\Api\Validators\GeneralValidator;

/**
 * Class GeneralRepositoryEloquent 
 */
class GeneralRepositoryEloquent  extends BaseRepository implements GeneralRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return General::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
