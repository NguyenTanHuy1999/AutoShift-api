<?php

namespace Gma\Acl\Traits;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

trait GmaRoleTrait
{
    public function cachedPermissions()
    {
        $rolePrimaryKey = $this->primaryKey;
        $cacheKey = 'gma_permissions_for_role_'.$this->$rolePrimaryKey;
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags('permission_roles')->remember($cacheKey, Config::get('cache.ttl', 60), function () {
                return $this->perms()->get();
            });
        } else {
            return $this->perms()->get();
        }
    }

    public function save(array $options = [])
    {
        if (!parent::save($options)) {
            return false;
        }
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags('permission_roles')->flush();
        }

        return true;
    }

    public function delete(array $options = [])
    {
        if (!parent::delete($options)) {
            return false;
        }
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags('permission_roles')->flush();
        }

        return true;
    }

    public function restore()
    {
        if (!parent::restore()) {
            return false;
        }
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags('permission_roles')->flush();
        }

        return true;
    }

    public function organization()
    {
        return $this->belongsTo('App\Api\Entities\Organization');
    }

    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Api\Entities\User', null, 'role_ids', 'user_ids');
    }

    /**
     * Many-to-Many relations with the permission model.
     * Named "perms" for backwards compatibility. Also because "perms" is short and sweet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function perms()
    {
        return $this->belongsToMany('App\Api\Entities\Permission', null, 'role_ids', 'permission_ids');
    }
    /**
     * Boot the role model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the role model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($role) {
            if (!method_exists('App\Api\Entities\Role', 'bootSoftDeletes')) {
                $role->users()->sync([]);
                $role->perms()->sync([]);
            }

            return true;
        });
    }
    /**
     * Checks if the role has a permission by its name.
     *
     * @param string|array $name       Permission name or array of permission names.
     * @param bool         $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function hasPermission($name, $organizationId = null)
    {
        foreach ($this->cachedPermissions() as $permission) {
            if ($permission->name == $name) {
                if (empty($organizationId)) {
                    return true;
                } else {
                    $organization = $permission->organization;
                    if ($organization && $organization->getKey() == $organizationId) {
                        return true;
                    }
                }

                return false;
            }
        }

        return false;
    }
    /**
     * Save the inputted permissions.
     *
     * @param mixed $inputPermissions
     */
    public function savePermissions($inputPermissions)
    {
        if (!empty($inputPermissions)) {
            $this->perms()->sync($inputPermissions);
        } else {
            $this->perms()->detach();
        }
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags('permission_roles')->flush();
        }
    }
    /**
     * Attach permission to current role.
     *
     * @param object|array $permission
     */
    public function attachPermission($permission)
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }
        if (is_array($permission)) {
            $permission = $permission['id'];
        }
        $this->perms()->attach($permission);
    }
    /**
     * Detach permission from current role.
     *
     * @param object|array $permission
     */
    public function detachPermission($permission)
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }
        if (is_array($permission)) {
            $permission = $permission['id'];
        }
        $this->perms()->detach($permission);
    }
    /**
     * Attach multiple permissions to current role.
     *
     * @param mixed $permissions
     */
    public function attachPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            $this->attachPermission($permission);
        }
    }
    /**
     * Detach multiple permissions from current role.
     *
     * @param mixed $permissions
     */
    public function detachPermissions($permissions = null)
    {
        if (!$permissions) {
            $permissions = $this->perms()->get();
        }
        foreach ($permissions as $permission) {
            $this->detachPermission($permission);
        }
    }
}
