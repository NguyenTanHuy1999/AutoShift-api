<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\TimekeepConfigRepository;
use App\Api\Repositories\Contracts\ShopRepository;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Shop;
use App\Api\Entities\TimekeepConfig;
use Illuminate\Support\Facades\Auth;

class TimekeepConfigController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $timekeepConfigRepository;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    protected $auth;

    protected $request;

    public function __construct(
        TimekeepConfigRepository $timekeepConfigRepository,
        ShopRepository $shopRepository,
        AuthManager $auth,
        Request $request
    ) {
        $this->timekeepConfigRepository = $timekeepConfigRepository;
        $this->shopRepository = $shopRepository;
        $this->request = $request;
        $this->auth = $auth;
        parent::__construct();
    }
    /**
     * @api {post} /shop/register 1. Register Shop
     * @apiDescription (Register Shop)
     * @apiGroup Employee
     * @apiParam {String} name  Name of user
     * @apiParam {Email} email  Login email.
     * @apiParam {String} password Login password
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * {
     *
     * }
     * }
     */

    #region tao timekeepConfig
    public function register()
    {
        // Validate Data import.
        $user = $this->user();
        $shop_id = $user->shop_id;
        $validator = \Validator::make($this->request->all(), [
            'wifi' =>'required',
            'location' => 'required',
            'imageRequire' =>'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }


        $wifi = $this->request->get('wifi');
        $location = $this->request->get('location');
        $timekeep_imageRequire = $this->request->get('imageRequire');


        $timekeeConfigCheck = TimekeepConfig::where(['wifi' => $wifi,'location'=>$location])->first();


        if (!empty($timekeeConfigCheck)) {
            return $this->errorBadRequest('Timekeep_config đã được sử dụng');
        }
        $attributes = [
            'wifi' =>$wifi,
            'location' =>$location,
            'imageRequire' =>$timekeep_imageRequire,
            'shop_id' =>$shop_id
        ];

        $timekeepConfig = $this->timekeepConfigRepository->create($attributes);
        return $this->successRequest(['data' => $timekeepConfig->transform()]);

        // return $this->successRequest($user->transform());
    }
    #endregion

    #region sua timekeepConfig
    public function update()
    {

        $user = $this->user();
        $shop_id = $user->shop_id;

        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'wifi' =>'required',
            'location' => 'required',
            'imageRequire' =>'required',
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id =  $this->request->get('id');
       
        $wifi = $this->request->get('wifi');
        $location = $this->request->get('location');
        $timekeep_imageRequire = $this->request->get('imageRequire');

        $timekeeConfigCheck = TimekeepConfig::where(['wifi' => $wifi,'location'=>$location])->first();


        if (!empty($timekeeConfigCheck)) {
            return $this->errorBadRequest('Wifi hoặc location đã bị trùng');
        }


        // lấy thông tin để sửa
        $attributes = [
            'wifi' =>$wifi,
            'location' =>$location,
            'imageRequire' =>$timekeep_imageRequire,
            'shop_id' =>$shop_id
        ];
        $timekeepConfig = $this->timekeepConfigRepository->update($attributes, $id);
        return $this->successRequest($timekeepConfig->transform());

    }
    #endregion
    public function list()
    {
        $user = $this->user();
        $timekeppConfig_list = TimekeepConfig::where(['shop_id' => $user->shop_id])->get();
        $data = [];
        foreach ($timekeppConfig_list as $timekeep) {
            $data[] = $timekeep->transform();
        }
        return $this->successRequest($data);
    }


    #region xem chi nhanh
    public function detail()
    {
        $user = $this->user();
        $timekeppConfig_list = TimekeepConfig::where(['shop_id' => $user->shop_id])->first();
        return $this->successRequest($timekeppConfig_list->transform());
    }
    #endregion
    public function delete()
    {
        $user = $this->user();
        $timekeppConfig_list = TimekeepConfig::where(['shop_id' => $user->shop_id])->delete();
        return $this->successRequest($timekeppConfig_list);
    }
}
