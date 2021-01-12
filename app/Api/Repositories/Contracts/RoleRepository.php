<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface RoleRepository.
 */
interface RoleRepository extends RepositoryInterface
{
    /**
     * Get a role.
     */
    public function getRole($name, $organizationId = null);

    /**
     * Add new role.
     *
     * @author 'Anh Pham'
     *
     * @param $name
     * @param $organizationId
     */
    public function addRole($name, $organizationId = null);
}
