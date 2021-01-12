<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\StudentRepository;
use App\Api\Entities\Student;
use App\Api\Validators\StudentValidator;

/**
 * Class StudentRepositoryEloquent
 */
class StudentRepositoryEloquent extends BaseRepository implements StudentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Student::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
