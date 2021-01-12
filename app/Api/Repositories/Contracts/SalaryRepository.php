<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface SalaryRepository
 */
interface SalaryRepository extends RepositoryInterface
{
    public function getSalary($params = [],$limit = 0);
}
