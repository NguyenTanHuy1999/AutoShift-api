<?php

namespace App\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Subject.
 *
 * @package namespace App\Api\Entities;
 */
class Subject extends Model implements Transformable
{
    use TransformableTrait;

    protected $table='subject';

    protected $connection = 'mysql';

    protected $guarded = [];

    public function transform()
    {
        return [

        ];
    }
}
