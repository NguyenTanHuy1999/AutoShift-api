<?php

namespace App\Api\Entities;

use App\Api\Transformers\RoleTransformer;
use Gma\Acl\Traits\GmaRoleTrait;
use Moloquent\Eloquent\Model as Moloquent;

class Role extends Moloquent
{
    use GmaRoleTrait;

    const TYPE_FRONTEND = 1;
    const TYPE_BACKEND = 2;
    const TYPE_ORGANIZATION = 3;

    // All Roles in System
    const ROLE_ADMIN = 'ADMIN';
    const ROLE_MANAGER = 'MANAGER';
    const ROLE_REGION = 'REGION';
    const ROLE_BRANCH = 'BRANCH';
    const ROLE_EMPLOYEE = 'EMPLOYEE';
    
    public $timestamp = false;

    protected $connection = 'mysql';
    protected $collection = 'roles';

    protected $fillable = ['name', 'display_name', 'description', 'dept'];

    public function transform()
    {
        $transformer = new RoleTransformer();

        return $transformer->transform($this);
    }
}
