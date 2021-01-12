<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\BranchRepository;
use App\Api\Repositories\Contracts\DepRepository;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\PositionController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Dep;
use App\Api\Entities\Branch;
use App\Api\Entities\User;

//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\Yaml\Tests\B;

class DepController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $branchRepository;

    /**
     * @var ShopRepository
     */
    protected $depRepository;

    protected $auth;

    protected $request;

    public function __construct(
        BranchRepository $branchRepository,
        DepRepository $depRepository,
        AuthManager $auth,
        Request $request
    ) {
        $this->branchRepository = $branchRepository;
        $this->depRepository = $depRepository;
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

    #region tao phong ban
    public function registerDep()
    {
        $user = $this->user();
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'branch_id' => 'required',
            'name' => 'required',
            'note' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $depname = $this->request->get('name');
        $branchCheck = Branch::where(['_id' => mongo_id($this->request->get('branch_id')),])->first();
        $depCheck = Dep::where(['name' => $depname, 'shop_id' => mongo_id($branchCheck->shop_id)])->first();

        // dd($depCheck->name);
        if (empty($branchCheck)) {
            return $this->errorBadRequest(trans('Chi nhánh không tồn tại'));
        } else {
            if (!empty($depCheck)) {
                return $this->errorBadRequest(trans('Phòng ban đã tồn tại'));
            }
        }

        $attributes = [
            'name' => $depname,
            'branch_id' => mongo_id($branchCheck->_id),
            'shop_id' => mongo_id($branchCheck->shop_id),
            'note' => $this->request->get('note')
        ];
        $dep = $this->depRepository->create($attributes);



        return $this->successRequest($dep->transform());

        // return $this->successRequest($user->transform());
    }
    #endregion

    #region xoa phong ban
    public function delDep()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id = $this->request->get('id');
        // Kiểm tra xem email đã được đăng ký trước đó chưa
        $idCheck = Dep::where(['_id' => $id])->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Phòng ban không tồn tại'));
        }
        $idCheck->delete();



        return $this->successRequest();

        // return $this->successRequest($user->transform());
    }
    #endregion

    #region sua phong ban
    public function editDep()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required',
            'name' => 'required',
            'note' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id = $this->request->get('id');
        // Kiểm tra xem email đã được đăng ký trước đó chưa

        $idCheck = Dep::where(['_id' => $id])->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Phòng ban không tồn tại'));
        }


        // Tạo shop trước
        $attributes = [
            'name' => $this->request->get('name'),
            'note' => $this->request->get('note')
        ];
        $dep = $this->depRepository->update($attributes, $id);



        return $this->successRequest($dep->transform());

        // return $this->successRequest($user->transform());
    }
    #endregion

    #region xem danh sach phong ban
    public function listDep()
    {
        $user = $this->user();
        $shop_id = $user->shop_id;
        // $validator = \Validator::make($this->request->all(), [
        //     'branch_id'=>'required',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->messages()->toArray());
        // }
        // $branch=Branch::where(['_id'=>$this->request->get('branch_id')])->first();
        // $branch_id=mongo_id($branch->_id);
        // dd($branchid);


        // $deps=$this->depRepository->all();
        $deps = Dep::where(['shop_id' => $shop_id])->get();
        // dd($deps);
        $data = [];
        foreach ($deps as $dep) {
            $data[] = $dep->transform();
        }
        return $this->successRequest($data);
    }
    #endregion

    public function list()
    {
        $is_all = (bool)$this->request->get('is_all');
        $params = [];
        $is_detail = false;
        if (!empty($this->request->get('id'))) {
            $is_detail = true;
            $params['is_detail'] = 1;
            $params['id'] = $this->request->get('id');
        } else {
            $params = ['is_paginate' => !$is_all];
        }
        $deps = $this->depRepository->getListDep($params, 30);
        if ($is_detail) {
            return $this->successRequest($deps->transform());
        }
        $data = [];
        if (!empty($deps)) {
            foreach ($deps as $dep) {
                $data[] = $dep->transform();
            }
        }
        return $this->successRequest($data);
    }

    public function deleteDep()
    {
        $id = $this->request->get('id');
        $dep = Dep::where('_id', mongo_id($id))->delete();
        return $this->successRequest($dep);
    }
    #region sua phòng ban
    public function updateDep()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required',
            'dep_name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id = $this->request->get('id');
        // Kiểm tra xem id đã được đăng ký trước đó chưa

        $idCheck = $this->depRepository->find(mongo_id($id))->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Phòng ban không tồn tại'));
        }
        // lấy thông tin để sửa
        $attributes = [
            'dep_name' => $this->request->get('dep_name'),
        ];
        $dep = $this->depRepository->update($attributes, mongo_id($id));
        return $this->successRequest($dep->transform());

        // return $this->successRequest($user->transform());
    }
    #endregion
}
