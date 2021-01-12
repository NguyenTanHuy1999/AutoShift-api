<?php

namespace Gma\Acl\Traits;

use App\Api\Entities\Role;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

trait GmaUserTrait
{
    /**
     * Checks if the user has a role by its name.
     *
     * @return bool
     */
    public function hasRole($name, $organizationId = null)
    {
        foreach ($this->cachedRoles() as $role) {
            if ($role->name == $name) {
                if ($role->type == Role::TYPE_BACKEND || $role->type == Role::TYPE_FRONTEND) {
                    return true;
                } else {
                    $organization = $role->organization;
                    if ($organization && $organization->getKey() == $organizationId) {
                        return true;
                    }
                }

                return false;
            }
        }
    }

    public function cachedRoles()
    {
        $userPrimaryKey = $this->primaryKey;
        $cacheKey = 'gma_roles_for_user_'.$this->$userPrimaryKey;
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags('user_roles')->remember($cacheKey, Config::get('cache.ttl'), function () {
                return $this->roles()->get();
            });
        } else {
            return $this->roles()->get();
        }
    }

    public function save(array $options = [])
    {
        // both inserts and updates
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags('user_roles')->flush();
        }

        return parent::save($options);
    }
    // public function delete(array $options = [])
    // {
    //     // soft or hard
    //     parent::delete($options);
    //     if (Cache::getStore() instanceof TaggableStore) {
    //         Cache::tags('user_roles')->flush();
    //     }
    // }
    // public function restore()
    // {
    //     // soft delete undo's
    //     parent::restore();
    //     if (Cache::getStore() instanceof TaggableStore) {
    //         Cache::tags('user_roles')->flush();
    //     }
    // }

    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Api\Entities\Role', null, 'user_ids', 'role_ids');
    }

    public function role()
    {
        return $this->belongsTo('App\Api\Entities\Role');
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param mixed $role
     */
    public function attachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->attach($role);
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param mixed $role
     */
    public function detachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->detach($role);
    }

    /**
     * Attach multiple roles to a user.
     *
     * @param mixed $roles
     */
    public function attachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->attachRole($role);
        }
    }

    /**
     * Detach multiple roles from a user.
     *
     * @param mixed $roles
     */
    public function detachRoles($roles = null)
    {
        if (!$roles) {
            $roles = $this->roles()->get();
        }

        foreach ($roles as $role) {
            $this->detachRole($role);
        }
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool         $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function can($permission, $requireAll = false)
    {
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->can($permName);
                if ($hasPerm && !$requireAll) {
                    return true;
                } elseif (!$hasPerm && $requireAll) {
                    return false;
                }
            }
            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->cachedRoles() as $role) {
                // Validate against the Permission table
                foreach ($role->cachedPermissions() as $perm) {
                    if (str_is($permission, $perm->name)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
