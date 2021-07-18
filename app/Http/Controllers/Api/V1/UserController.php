<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Entities\User;
use App\Api\Entities\Shop;
use App\Api\Entities\Position;
use App\Api\Entities\Dep;
use App\Api\Entities\Branch;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\ShopRepository;
use App\Api\Repositories\Contracts\PositionRepository;
use Laravel\Lumen\Routing\Controller as BaseController;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    protected $request;

    protected $auth;

    public function __construct(
        UserRepository $userRepository,
        ShopRepository $shopRepository,
        AuthManager $auth,
        Request $request
    ) {
        $this->userRepository = $userRepository;
        $this->shopRepository = $shopRepository;
        $this->request = $request;
        $this->auth = $auth;

        parent::__construct();
    }


    public function createUser()
    {
        if ($this->request->isMethod('POST')) {
            $validator = \Validator::make($this->request->all(), [
                'name' => 'required',
                'position_id' => 'nullable',
                'email' => 'nullable',
                'dep_id' => 'nullable',
                'phone_number' => 'required',
                'basic_salary' => 'nullable',
                'is_admin' => 'nullable',
                'sex' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->messages()->toArray());
            }
            if ($this->request->hasFile('avatar')) {
                $avatar_file = $this->request->file('avatar');
                $avatar_url=  uploadImage($avatar_file);
                $email = strtolower($this->request->get('email'));
                $basic_salary = $this->request->get('basic_salary');
                $userAttributes = [
                    'name' => $this->request->get('name'),
                    'avatar' => $avatar_url,
                    'email' => $email,
                    'position_id' => mongo_id($this->request->get('position_id')),
                    'dep_id' => mongo_id($this->request->get('dep_id')),
                    'is_root' => 0,
                    'is_admin' => $this->request->get('is_admin'),
                    'phone_number' => $this->request->get('phone_number'),
                    'basic_salary' => $basic_salary,
                    'shop_id' => $this->user()->shop_id,
                    'sex' => $this->request->get('sex'),
                    'birth' => $this->request->get('birth'),
                ];
                $user = $this->userRepository->create($userAttributes);
                return $this->successRequest($user->transform());
            }
            else{
                $email = strtolower($this->request->get('email'));
                $basic_salary = $this->request->get('basic_salary');
                $userAttributes = [
                    'name' => $this->request->get('name'),
                    'avatar' => null,
                    'email' => $email,
                    'position_id' => mongo_id($this->request->get('position_id')),
                    'dep_id' => mongo_id($this->request->get('dep_id')),
                    'is_root' => 0,
                    'is_admin' => $this->request->get('is_admin'),
                    'phone_number' => $this->request->get('phone_number'),
                    'basic_salary' => $basic_salary,
                    'shop_id' => $this->user()->shop_id,
                    'sex' => $this->request->get('sex'),
                    'birth' => $this->request->get('birth'),
                ];
                $user = $this->userRepository->create($userAttributes);
                return $this->successRequest($user->transform());
            }
        }
    }    


    //Get detail user current
    public function userShow()
    {
        $user = $this->user();
        $data = $user->transform('with-shop');

        //Save history login
        $date = Carbon::now();
        $user->visited_date = $date;
        $user->vistied_ip = get_client_ip();
        $user->save();
        return $this->successRequest($data);
    }

    public function list()
    {
        $user = $this->user();
        $shop_id = $user->shop_id;

        $data = [];

        $listUser = User::where(['shop_id' => $shop_id])->get();
        if (!empty($listUser)) {
            foreach ($listUser as $user) {
                $data[] = $user->transform();
            }
        }
        return $this->successRequest($data);
    }

    /**
     * @api {post}/user/update 2. update my info
     * @apiDescription Update my info
     * @apiGroup user
     * @apiPermission JWT
     * @apiVersion 0.1.0
     * @apiParam {String} [name] name
     * @apiParam {String} [email] name
     * @apiParam {Object} [company] company[phone], company[address]...
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
                "error_code": 0,
                "message": [
                    "Successfully"
                ],
                "data": {
                    "id": "p5jFuwDbo84KteeCc",
                    "name": "Trung Hà",
                    "username": "+84909224002",
                    "phone": "+84909224002",
                    "phone_code": "84",
                    "company": {
                        "phone": "0909090909",
                        "address": "Bùi Hữu Nghĩa, Bình Thạnh"
                    },
                    "is_supplier": 0
                }
            }
     */
    public function update()
    {
        $user = $this->userRepository->find($this->request->get('id'));
        if ($this->request->isMethod('POST')) {
            $validator = \Validator::make($this->request->all(), [
                'name' => 'required',
                'position_id' => 'nullable',
                'email' => 'nullable',
                'dep_id' => 'nullable',
                'phone_number' => 'required',
                'basic_salary' => 'nullable',
                'is_admin' => 'nullable',
                'sex' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->messages()->toArray());
            }
            if ($this->request->hasFile('avatar')) {
                $avatar_file = $this->request->file('avatar');
                $avatar_url=  uploadImage($avatar_file);
                $email = strtolower($this->request->get('email'));
                $basic_salary = $this->request->get('basic_salary');
                $userAttributes = [
                    'name' => $this->request->get('name'),
                    'avatar' => null,
                    'email' => $email,
                    'position_id' => mongo_id($this->request->get('position_id')),
                    'dep_id' => mongo_id($this->request->get('dep_id')),
                    'is_root' => 0,
                    'is_admin' => $this->request->get('is_admin'),
                    'phone_number' => $this->request->get('phone_number'),
                    'basic_salary' => $basic_salary,
                    'shop_id' => $this->user()->shop_id,
                    'sex' => $this->request->get('sex'),
                    'birth' => $this->request->get('birth'),
                ];
                $user = $this->userRepository->update($userAttributes, $user->_id);
                return $this->successRequest($user->transform());
            }
            else{
                $email = strtolower($this->request->get('email'));
                $basic_salary = $this->request->get('basic_salary');
                $userAttributes = [
                    'name' => $this->request->get('name'),
                    'avatar' => null,
                    'email' => $email,
                    'position_id' => mongo_id($this->request->get('position_id')),
                    'dep_id' => mongo_id($this->request->get('dep_id')),
                    'is_root' => 0,
                    'is_admin' => $this->request->get('is_admin'),
                    'phone_number' => $this->request->get('phone_number'),
                    'basic_salary' => $basic_salary,
                    'shop_id' => $this->user()->shop_id,
                    'sex' => $this->request->get('sex'),
                    'birth' => $this->request->get('birth'),
                ];
                $user = $this->userRepository->update($userAttributes, $user->_id);
                return $this->successRequest($user->transform());
            }
        }
    }

    /**
     * @api {GET} /user/info/{username} 3. User Info
     * @apiDescription Get user info
     * @apiGroup user
     * @apiPermission JWT
     * @apiVersion 0.1.0
     * @apiParam {String} username  username's user
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *      "error_code": 0,
                "message": [
                    "Successfully"
                ],
                "data": [
                    {
                        "id": "oGpZf8tSv3FNLHZv4",
                        "name": "saritvn",
                        "username": "saritvn",
                        "phone": "0909224002",
                        "phone_code": "84",
                        "company": {
                            "name": "Green Mobile App",
                            "address": "195 Dien Bien Phu, Ward 15, Binh Thanh Distric, Ho Chi Minh City",
                            "email": "trung.ha@greenapp.vn",
                            "phone": "0909224002",
                            "field": "Mobile App"
                        }
                    }
                ]
     *     }
     */

    public function info(Request $request, $username)
    {
        // Validate HEADER import.
        // $validator = \Validator::make($request->all(), [
        //     'username'   => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->messages()->toArray());
        // }

        $user = $this->userRepository->findByField('username', $username)->first();
        if (empty($user)) {
            return $this->successRequest([]);
        }
        $data = $user->transform();
        return $this->successRequest($data);
    }
    public function deleteUser($id)
    {
        $id = $this->request->get('id');
        try {
            $delete_user = User::where('_id', $id)->delete();
        } catch (\Exception $e) {
            return $this->errorBadRequest($e->messages()->toArray());
        }
        return $this->successRequest('Đã xóa thành công');
    }


    public function createUser1()
    {
        if ($this->request->isMethod('POST')) {
            $validator = \Validator::make($this->request->all(), [
                'name' => 'required',
                'position_id' => 'nullable',
                'email' => 'nullable',
                'dep_id' => 'nullable',
                'branch_id' => 'nullable',
                'phone_number' => 'required',
                'timekeep_config' => 'required',
                'basic_salary' => 'required',
                'sex' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->messages()->toArray());
            }
            if ($this->request->hasFile('avatar')) {
                $avatar_file = $this->request->file('avatar');
                $avatar_url=  uploadImage($avatar_file);
                $email = strtolower($this->request->get('email'));
                $timekeep_config = $this->request->get('timekeep_config');
                $basic_salary = $this->request->get('basic_salary');
                $userAttributes = [
                    'name' => $this->request->get('name'),
                    'avatar' => $avatar_url,
                    'email' => $email,
                    'position_id' => mongo_id($this->request->get('position_id')),
                    'branch_id' => mongo_id($this->request->get('branch_id')),
                    'dep_id' => mongo_id($this->request->get('dep_id')),
                    'is_root' => 1,
                    'phone_number' => $this->request->get('phone_number'),
                    'timekeep_config' =>$timekeep_config,
                    'basic_salary' => $basic_salary,
                    'shop_id' => $this->user()->shop_id,
                    'sex' => $this->request->get('sex'),
                    'birth' => $this->request->get('birth'),
                ];
                $user = $this->userRepository->create($userAttributes);
                return $this->successRequest($user->transform());
            }
            else{
                return $this->errorBadRequest(trans('Lỗi về chọn avatar'));
            }
        }
    }


    // //Bỏ chỗ này
    // public function createUser2()
    // {

    //     $validator = \Validator::make($this->request->all(), [
    //         'full_name' => 'required',
    //         'is_root' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return $this->errorBadRequest($validator->messages()->toArray());
    //     }
    //     $position = $this->request->get('position');
    //     $email = strtolower($this->request->get('email'));
    //     // Kiểm tra xem email đã được đăng ký trước đó chưa
    //     $userCheck = User::where(['email' => $email])->first();
    //     if (!empty($userCheck)) {
    //         return $this->errorBadRequest(trans('user.email_exists'));
    //     }
    //     //Lấy shop và position để thêm user vào
    //     $branch = Branch::where(['_id' => mongo_id($this->request->get('branch_id'))])->first();
    //     if (empty($branch)) {
    //         return $this->errorBadRequest(trans('Chưa có chi nhánh'));
    //     }
    //     $position = Position::where(['_id' => mongo_id($this->request->get('position_id'))])->first();
    //     if (empty($position)) {
    //         return $this->errorBadRequest(trans('Chưa có vị trí'));
    //     }
    //     $dep = Dep::where(['_id' => mongo_id($this->request->get('dep_id'))])->first();
    //     if (empty($dep)) {
    //         return $this->errorBadRequest(trans('Chưa có phòng ban'));
    //     }

    //     $userAttributes = [
    //         'full_name' => $this->request->get('full_name'),
    //         'email' => $email,
    //         'is_web' => (int)($this->request->get('is_web')),
    //         'shop_id' => mongo_id($branch->shop_id),
    //         'position_id' => mongo_id($position->_id),
    //         'branch_id' => mongo_id($branch->_id),
    //         'dep_id' => mongo_id($dep->_id),
    //         'is_root' => $this->request->get('is_root'),
    //     ];
    //     $user = $this->userRepository->create($userAttributes);
    //     $token = $this->auth->fromUser($user);
    //     return $this->successRequest($token);
    // }
}


