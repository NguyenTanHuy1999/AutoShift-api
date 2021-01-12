<?php

namespace Gma\Acl\Traits;

trait GmaPermissionTrait
{
    /**
     * Many-to-Many relations with role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Api\Entities\Role');
    }
    /**
     * Boot the permission model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the permission model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($permission) {
            if (!method_exists('App\Api\Entities\Permission', 'bootSoftDeletes')) {
                $permission->roles()->sync([]);
            }

            return true;
        });
    }

    public function organization()
    {
        return $this->belongsTo('App\Api\Entities\Organization');
    }
}
