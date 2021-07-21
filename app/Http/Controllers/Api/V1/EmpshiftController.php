<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Entities\Shift;
use App\Api\Repositories\Contracts\EmpshiftRepository;
use App\Api\Repositories\Contracts\UserRepository;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\User;
use App\Api\Entities\Empshift;

//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\Yaml\Tests\B;

class EmpshiftController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $empshiftRepository;

    protected  $userRepository;


    protected $auth;

    protected $request;

    public function __construct(
        EmpshiftRepository $empshiftRepository,
        UserRepository $userRepository,
        AuthManager $auth,
        Request $request
    ) {
        $this->empshiftRepository = $empshiftRepository;
        $this->userRepository = $userRepository;
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

    #region nhan vien dang ky ca
    public function registerEF()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'user_id' => 'required',
            'shift_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $user_id = $this->request->get('user_id');
        $shift_id = $this->request->get('shift_id');
        $userCheck = User::where(['_id' => mongo_id($user_id)])->first();
        $shiftCheck = Shift::where(['_id' => $shift_id])->first();
        if (empty($userCheck)) {
            return $this->errorBadRequest('Nhân viên không tồn tại');
        }
        if (empty($shiftCheck)) {
            return $this->errorBadRequest('Ca làm chưa đăng ký');
        }
        $attributes = [
            'user_id' => $user_id,
            'shift_id' => $shift_id,
        ];
        $empShift = $this->empshiftRepository->create($attributes);


        return $this->successRequest($empShift->transform());

        // return $this->successRequest($user->transform());
    }
    #endregion



    #region xem ca
    public function viewEF()
    {
        // Validate Data import.
        /*$validator = \Validator::make($this->request->all(), [
            'branchname'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $branchname=$this->request->get('branchname');
        // Kiểm tra xem email đã được đăng ký trước đó chưa

        $branchCheck=Branch::where(['branchName'=>$branchname])->first();
        if(empty($branchCheck)) {
            return $this->errorBadRequest(trans('Chi nhánh không có sẵn'));
        }

        $branchid=mongo_id($branchCheck->_id);

        $dep = Dep::where(['branch_id'=>$branchid])->paginate();



        return $this->successRequest($dep);*/
        $user = $this->user();
        $empshift = $this->empshiftRepository->getEmpshift(["user_id" => $user->_id]);
        // dd($empshift);
        $data = [];
        foreach ($empshift as $ef) {
            $data[] = $ef->transform();
        }
        return $this->successRequest($data);

        // return $this->successRequest($user->transform());
    }
    public function listShiftbyUser()
    {
        $user_id = $this->user()->_id;
        $shifts = $this->empshiftRepository
            ->orderBy('working_date', 'desc')->findbyField('user_id', mongo_id($user_id));
        $data = [];
        foreach ($shifts as $shift) {
            $data[] = $shift->transform();
        }
        return $this->successRequest($data);
    }

    public function listShiftTimeSheet()
    {
        $shop_id = $this->user()->shop_id;
        $from_date = Carbon::parse($this->request->get('from_date'));
        $to_date = Carbon::parse($this->request->get('to_date'));
        //Lấy danh sách user
        $user_list = User::where((['shop_id' => $shop_id, 'is_root' => 0]))->get();
        // dd($shop_id);
        //Lấy danh sách ca
        $listEmpShift = Empshift::where('working_date', '>=', $from_date)
            ->where('working_date', '<=', $to_date)->get();






        $data = [];
        $weekMap = [
            1 => 'MON',
            2 => 'TUE',
            3 => 'WED',
            4 => 'THU',
            5 => 'FRI',
            6 => 'SAT',
            7 => 'SUN',
        ];
        foreach ($user_list as $user) {
            $rowData = [
                'user' => $user->transform(),
            ];
            foreach ($weekMap as $value) {

                // djson($listEmpShift);
                $rowData[$value] = $this->getShiftByWeek($value, $listEmpShift, $user);
            }

            $data[] = $rowData;
        }
        return $this->successRequest($data);
    }

    public function getShiftByWeek($day_of_week, $listEmpShift, $user)
    {
        $data = [];
        // djson($day_of_week);
        foreach ($listEmpShift as $val) {
            // dd($val->dayOfWeek == $day_of_week);
            if ($val->dayOfWeek == $day_of_week &&  $val->user_id == $user->_id) {
                $data[] = $val;
            }
        }

        return $data;
    }
    #endregion
}
