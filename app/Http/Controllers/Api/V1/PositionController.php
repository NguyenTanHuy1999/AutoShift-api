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
        ]);
        $shop_id = $this->user()->shop_id;
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        $shop = Shop::where(['_id' => $shop_id])->first();
        $position_name = $this->request->get('position_name');
        $positioncheck = Position::where('shop_id','=',$shop_id)->where('position_name','=',$position_name)->first();
        if (empty($shop)) {
            return $this->errorBadRequest(trans('Chưa có Shop'));
        }
        if (!(empty($positioncheck))){
            return $this->errorBadRequest('Trùng position');
        }
        $attribute = [
            'shop_id' => $shop_id,
            'position_name' => $this->request->get('position_name'),
        ];
        $position = $this->positionRepository->create($attribute);
        $data = $position->transform();
        return $this->successRequest($data);
    }
    public function editPosition()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required',
            'position_name' => 'required',
        ]);
      
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        $shop_id = $this->user()->shop_id;
        $id = $this->request->get('id');
        // Kiểm tra xem Position đã được đăng ký trước đó chưa
        $idCheck = Position::where(['_id' => $id])->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Position không tồn tại'));
        }
        $position_name = $this->request->get('position_name');
        $positioncheck = Position::where('shop_id','=',$shop_id)->where('position_name','=',$position_name)->first();

        if (!(empty($positioncheck))){
            return $this->errorBadRequest('Trùng position');
        }
        $attribute = [
            'shop_id' => $shop_id,
            'position_name' => $this->request->get('position_name'),
        ];
        $position = $this->positionRepository->update($attribute, $id);

        return $this->successRequest($position->transform());

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
