<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface HistoryRepository
 */
interface HistoryRepository extends RepositoryInterface
{
    public function getHistory($params = [],$limit = 0);
}
