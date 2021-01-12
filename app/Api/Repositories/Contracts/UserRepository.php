<?php

namespace App\Api\Repositories\Contracts;

use App\Api\Entities\User;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserRepository.
 */
interface UserRepository extends RepositoryInterface
{
	public function getListUser($params = [], $limit = 0);
	public function getRootUser($params = [], $limit = 0);
}
