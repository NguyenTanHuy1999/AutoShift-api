<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EmpshiftRepository
 */
interface EmpshiftRepository extends RepositoryInterface
{
    public function getEmpshift($params = [],$limit = 0);
}
