<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ShiftRepository
 */
interface ShiftRepository extends RepositoryInterface
{
    public function getShift($params = [],$limit = 0);
}
