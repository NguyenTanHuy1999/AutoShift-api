<?php

namespace App\Api\Transformers;

use App\Api\Entities\User;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use App\Api\Entities\Organization;
use App\Api\Entities\Element;
use App\Api\Entities\UserCountry;
use App\Api\Entities\UserProvince;
use App\Api\Entities\UserTown;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Class UserTransformer.
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * @var request
     */
    protected $request;

    public function __construct()
    {
        $request = App::make(Request::class);
    }

    /**
     * Transform the \User entity.
     *
     * @param \User $model
     *
     * @return array
     */
    public function transform(User $model, $type = '')
    {

        $data = array(
            'id' => $model->_id,
            'name' => $model->name,
            'full_name' => $model->full_name,
            'phone_number' => $model->phone_number,
            'email' => $model->email,
            'is_root' => (int)$model->is_root,
            'sex' => (int) $model->sex,
            'shop_id' => mongo_id_string($model->shop_id),
            'position_id' => mongo_id_string($model->position_id),
            'branch_id' => mongo_id_string($model->branch_id),
            'dep_id' => mongo_id_string($model->dep_id),
            'shop' => [],
            'position' => [],
            'branch' => [],
            'dep' => [],
        );
        $shop = $model->shop();
        $position = $model->position();
        $branch = $model->branch();
        $dep = $model->dep();
        if (!empty($shop)) {
            $data['shop'] = $shop->transform();
        }
        if (!empty($position)) {
            $data['position'] = $position->transform();
        }
        if (!empty($branch)) {
            $data['branch'] = $branch->transform();
        }
        if (!empty($dep)) {
            $data['dep'] = $dep->transform();
        }
        return $data;
    }
}
