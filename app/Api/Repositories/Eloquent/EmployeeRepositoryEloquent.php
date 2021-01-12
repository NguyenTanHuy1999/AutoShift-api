<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\EmployeeRepository;
use App\Api\Entities\Employee;
use App\Api\Validators\EmployeeValidator;

/**
 * Class EmployeeRepositoryEloquent
 */
class EmployeeRepositoryEloquent extends BaseRepository implements EmployeeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Employee::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
