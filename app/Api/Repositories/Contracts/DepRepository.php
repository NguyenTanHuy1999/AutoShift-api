<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface DepRepository
 */
interface DepRepository extends RepositoryInterface
{

//    public function deleteDep($id,$limit =0);

    public function getDep($params = [],$limit = 0);
    public function getListDep($params = [],$limit = 0);
}
