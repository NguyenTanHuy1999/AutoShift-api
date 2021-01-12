<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\SubjectRepository;
use App\Api\Entities\Subject;
use App\Api\Validators\SubjectValidator;

/**
 * Class SubjectRepositoryEloquent
 */
class SubjectRepositoryEloquent extends BaseRepository implements SubjectRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Subject::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
