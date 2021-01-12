<?php


namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\PositionRepository;
use App\Api\Repositories\Contracts\ShopRepository;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Position;
use App\Api\Entities\Shop;
use App\Api\Entities\User;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\Yaml\Tests\B;

class PositionController extends Controller
{
    protected $positionRepository;
    protected $shopRepository;
    protected $request;
    public function __construct(Request $request, PositionRepository $positionRepository, ShopRepository $shopRepository)
    {
        $this->request = $request;
        $this->positionRepository = $positionRepository;
        $this->shopRepository = $shopRepository;
        parent::__construct();
    }
    public function createPosition()
    {
        $validator = \Validator::make($this->request->all(), [
            'position_name' => 'required',
            'shop_id' => 'required',
            'permission' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        $shop = Shop::where(['_id' => mongo_id($this->request->get('shop_id'))])->first();
        $positioncheck = Position::where(['position_name' => strtolower($this->request->get('position_name'))])->first();
        if (empty($shop)) {
            return $this->errorBadRequest(trans('Chưa có Shop'));
        } else {
            if (!empty($positioncheck))
                return $this->errorBadRequest('Trùng position');
        }
        $attribute = [
            'shop_id' => mongo_id($shop->_id),
            'position_name' => $this->request->get('position_name'),
            'permission' => (int)$this->request->get('permission'),
        ];
        $position = $this->positionRepository->create($attribute);
        $data = $position->transform();
        return $this->successRequest($data);
    }
    public function deletePosition()
    {
        $id = $this->request->get('id');
        $deleted_position = Position::where('_id', $id)->delete();
        return ($deleted_position);
    }
    public function listPosition()
    {
        $positions = $this->positionRepository->all();
        $data = [];
        foreach ($positions as $position) {
            $data[] = $position->transform();
        }
        return $this->successRequest($data);
    }
}
