<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\ShopRepository;
use App\Api\Repositories\Contracts\TimekeepConfigRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Shop;
use App\Api\Entities\User;
use App\Api\Entities\TimekeepConfig;

//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;
    protected $timekeepConfigRepository;

    protected $auth;

    protected $request;

    public function __construct(
        UserRepository $userRepository,
        ShopRepository $shopRepository,
        TimekeepConfigRepository $timekeepConfigRepository,
        
        AuthManager $auth,
        Request $request
    ) {
        $this->userRepository = $userRepository;
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
    public function registerShop()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'name' => 'required',
            'phone_number' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        $email = strtolower($this->request->get('name'));;
        // Ki???m tra xem email ???? ???????c ????ng k?? tr?????c ???? ch??a
        $userCheck = User::where(['email' => $email])->first();
        if (!empty($userCheck)) {
            return $this->errorBadRequest(trans('user.email_exists'));
        }

        // T???o shop tr?????c
        $attributes = [
            'name' => $this->request->get('name'),
            'shop_name' => $this->request->get('shop_name'),
            'email' => $this->request->get('name'),
        ];
        $shop = $this->shopRepository->create($attributes);

        $alias = remove_sign($this->request->get('name'));
        // Sau ???? t???o user
        $userAttributes = [
            'email' => $email,
            'is_web' => (int)($this->request->get('is_web')),
            'shop_id' => mongo_id($shop->_id),
            'position_id' => null,
            //'branch_id' => null,
            'dep_id' => null,
            'basic_salary' =>null,
            'is_root' => 1,
            'is_admin' =>1,
            'sex' => null,
            'name' => 'Root',
            'alias' => $alias,
            'phone_number' => $this->request->get('phone_number')
        ];
        $user = $this->userRepository->create($userAttributes);

        $wifi = [
            'ssid' =>null,
            'bssid' =>null,
            'require' =>false
        ];
        $location =[
            'long' =>null,
            'lat' =>null,
            'address' =>null,
            'r' =>500,
            'require' =>false
        ];
        $attributes = [
            'wifi' =>$wifi,
            'location' =>$location,
            'imageRequire' =>null,
            'shop_id' =>mongo_id($shop->_id)
        ];

        $timekeepConfig = $this->timekeepConfigRepository->create($attributes);


        //G??n token v??o user
        $token = $this->auth->fromUser($user);
        $userTrans = $user->transform();
        return $this->successRequest(['token' => $token, 'user' => $userTrans]);
    }
    public function viewShop()
    {
        $data = Shop::all();
        //        $data=$this->shopRepository->getShop(["shop_name"=>$this->request->get('shop_name'),"shop_id"=>$this->request->get('id')]);
        return $this->successRequest($data);
    }
    public function editShop()
    {
        $id = $this->request->get('id');
        $shop = $this->shopRepository->find($id);
        if ($this->request->method('POST')) {
            $email = strtolower($this->request->get('email'));
            $shopAttributes = [
                'name' => $shop->_id,
                'shop_name' => $this->request->get('shop_name'),
                'email' => $email,
                'is_web' => (int)($this->request->get('is_web'))
            ];
            $data = $this->shopRepository->update($shopAttributes, $id);
            return $this->successRequest($data);
        }
        return $this->successRequest($shop);
    }
    public function deleteShop()
    {
        $id = $this->request->get('id');
        $user = $this->shopRepository->deleteShop($id);
        return $this->successRequest($user);
        //        $user=$this->userRepository->findByField('shop_id',mongo_id($id));
        //        $shop=$this->shopRepository->find($id);
        //        foreach($user as $row)
        //        {
        //            $data=$this->userRepository->delete($row->_id);
        //        }
        //        $data=$this->shopRepository->delete($shop->_id);
        //        return $this->successRequest('X??a th??nh c??ng');
    }
    //    public function viewShop()
    //    {
    //
    //        $data=$this->shopRepository->getShop(["name"=>$this->request->get('id')]);
    //        return $this->successRequest($data);
    //    }
    public function searchShop()
    {
        if (!empty($this->request->get('id'))) {
            $params = [
                'is_detail' => true,
                'shop_id' => $this->request->get('id')
            ];
        } else
            $params = [
                'is_detail' => true,
                'shop_id' => mongo_id($this->auth->getPayload()->get('shop_id'))
            ];
        $shop = $this->shopRepository->getShop($params);
        $shopTrans = $shop->transform();
        $shopTrans['packages'] = $shop->validatePackage('', 1);
        //T???m th???i set =4 do client ??ang l???i
        $shopTrans['get_started_step'] = 4;
        return $this->successRequest($shopTrans);
    }
}
