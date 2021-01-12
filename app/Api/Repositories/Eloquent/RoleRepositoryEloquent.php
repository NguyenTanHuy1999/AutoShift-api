<?php

namespace App\Api\Repositories\Eloquent;

use App\Api\Entities\Role;
use App\Api\Repositories\Contracts\RoleRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class RoleRepositoryEloquent.
 */
class RoleRepositoryEloquent extends BaseRepository implements RoleRepository, CacheableInterface
{
    use CacheableRepository;


    private $_names = array(
        Role::ROLE_ADMIN => 'Administrator',
        Role::ROLE_MANAGER => 'Manager',
        Role::ROLE_REGION => 'Region Manager',
        Role::ROLE_BRANCH => 'Branch Manager',
        Role::ROLE_EMPLOYEE => 'Employee'
        );
    public function __construct(Application $app)
    {

        parent::__construct($app);
    }

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
    }

    /**
     * Get a role.
     */
    public function getRole($name, $organizationId = null)
    {
        $where = array(
            'name' => $name,
            );

        if (!empty($organizationId)) {
            $where['organization_id'] = $organizationId;
        }

        $item = $this->findWhere($where)->first();

        return $item;
    }

    /**
     * Add new role.
     *
     * @author 'Anh Pham'
     *
     * @param $name
     * @param $organizationId
     */
    public function addRole($name, $organizationId = null)
    {
        $organizationId = mongo_id($organizationId);
        $displayName = $this->_names[$name];
        $attributes = array(
            'organization_id' => $organizationId,
            'name' => $name,
            'display_name' => $displayName,
            );

        $role = $this->create($attributes);

        return $role;
    }
}
