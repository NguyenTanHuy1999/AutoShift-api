<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\WifiConfigTransformer;
use Moloquent\Eloquent\SoftDeletes;

class WifiConfig extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'wifi_config';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $guarded = array();

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transform()
    {
        $transformer = new WifiConfigTransformer();

        return $transformer->transform($this);
    }

    public function branch()
    {
        $branch = null;
        if (!empty($this->branch_id)) {
            $branch = Branch::where(['_id' => $this->branch_id])->first();
        }
        return $branch;
    }
    public function dep()
    {
        $dep = null;
        if (!empty($this->dep_id)) {
            $dep = Dep::where(['_id' => mongo_id($this->dep_id)])->first();
        }
        return $dep;
    }
}
