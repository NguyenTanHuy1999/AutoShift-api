<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\WifiConfigRepository;
use App\Api\Repositories\Contracts\ShopRepository;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Shop;
use App\Api\Entities\WifiConfig;
use Illuminate\Support\Facades\Auth;

class WifiClockConfigController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $wifiConfigRepository;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    protected $auth;

    protected $request;

    public function __construct(
        WifiConfigRepository $wifiConfigRepository,
        ShopRepository $shopRepository,
        AuthManager $auth,
        Request $request
    ) {
        $this->wifiConfigRepository = $wifiConfigRepository;
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

    #region tao wifi
    public function register()
    {
        // Validate Data import.
        $user = $this->user();
        $shop_id = $user->shop_id;
        // dd($shop_id);
        $validator = \Validator::make($this->request->all(), [
            'name' => 'required',
            'bssid' => 'required',
            'ssid' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }


        $wifi_name = $this->request->get('name');
        $wifi_ssid = $this->request->get('ssid');
        $wifi_bssid = $this->request->get('bssid');
        $branch_id = $this->request->get('branch_id');
        $dep_id = $this->request->get('dep_id');


        $wifiCheck = WifiConfig::where(['bssid' => $wifi_bssid])->first();


        if (!empty($wifiCheck)) {
            return $this->errorBadRequest('Wifi đã được sử dụng để chấm công');
        }

        $attributes = [
            'name' => $wifi_name,
            'bssid' => $wifi_bssid,
            'ssid' => $wifi_ssid,
            'branch_id' => mongo_id($branch_id),
            'dep_id' => mongo_id($dep_id),
            'shop_id' => mongo_id($shop_id)
        ];


        $wifi = $this->wifiConfigRepository->create($attributes);
        return $this->successRequest(['data' => $wifi->transform()]);

        // return $this->successRequest($user->transform());
    }
    #endregion

    #region sua wifi
    public function update()
    {

        $user = $this->user();
        $shop_id = $user->shop_id;

        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'name' => 'required',
            'bssid' => 'required',
            'ssid' => 'required',
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id =  $this->request->get('id');

        $wifi_name = $this->request->get('name');
        $wifi_ssid = $this->request->get('ssid');
        $wifi_bssid = $this->request->get('bssid');
        $branch_id = $this->request->get('branch_id');
        $dep_id = $this->request->get('dep_id');


        // lấy thông tin để sửa
        $attributes = [
            'name' => $wifi_name,
            'bssid' => $wifi_bssid,
            'ssid' => $wifi_ssid,
            'branch_id' => $branch_id,
            'dep_id' => $dep_id,
            'shop_id' => mongo_id($shop_id)

        ];
        $wifi = $this->wifiConfigRepository->update($attributes, $id);
        return $this->successRequest($wifi->transform());

        // return $this->successRequest($user->transform());
    }
    #endregion
    public function list()
    {
        $user = $this->user();
        // $branches=$this->branchRepository->all();
        $wifi_list = WifiConfig::where(['shop_id' => $user->shop_id])->get();
        // dd($branches);
        $data = [];
        foreach ($wifi_list as $wifi) {
            $data[] = $wifi->transform();
        }
        return $this->successRequest($data);
    }


    #region xem chi nhanh
    public function detail()
    {
        $id = $this->request->get('id');
        $wifi = WifiConfig::where(['_id' => $id])->first();
        return $this->successRequest($wifi->transform());
    }
    #endregion
    public function delete()
    {
        $id = $this->request->get('id');
        $branch = WifiConfig::where('_id', mongo_id($id))->delete();
        return $this->successRequest($branch);
    }
}
