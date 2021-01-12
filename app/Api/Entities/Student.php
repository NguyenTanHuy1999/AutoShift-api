<?php

namespace App\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Student.
 *
 * @package namespace App\Api\Entities;
 */
class Student extends Model implements Transformable
{
    use TransformableTrait;

    protected $table='students';

    protected $connection = 'mysql';

    protected $guarded = [];

    public function transform()
    {
        return [

        ];
    }

}
