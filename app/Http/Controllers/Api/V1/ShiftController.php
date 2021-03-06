<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Entities\Branch;
use App\Api\Entities\Dep;
use App\Api\Entities\Empshift;
use App\Api\Repositories\Contracts\ShiftRepository;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Shift;
use App\Api\Entities\User;
use App\Api\Repositories\Contracts\EmpshiftRepository;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Contracts\Logging\Log as LoggingLog;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Yaml\Tests\B;

class ShiftController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $shiftRepository;

    /**
     * @var ShopRepository
     */
    protected $dayOfWorkRepository;

    protected $auth;

    protected $request;

    public function __construct(
        ShiftRepository $shiftRepository,
        AuthManager $auth,
        Request $request,
        EmpshiftRepository $empshiftRepository
    ) {
        $this->shiftRepository = $shiftRepository;
        $this->request = $request;
        $this->auth = $auth;
        $this->empshiftRepository = $empshiftRepository;
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

    #region tao ca lam
    public function registerShift()
    {
        $user = $this->user();
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'branch_ids' => 'nullable',
            'dep_ids' => 'nullable',
            'position_ids' => 'nullable',
            'name' => 'required',
            'assignments' => 'required',
            'shift_key' => 'required',
            'work_day' => 'required',
            'is_OT' => 'required',
            'time_begin' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i',
            // 'work_date_begin' => 'required|date_format:Y-m-d',
            // 'work_date_end' => 'required|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }


        //L???y data
        $shift_key = $this->request->get('shift_key');
        $dep_ids = $this->request->get('dep_id');
        $branch_ids = $this->request->get('branch_id');
        $position_id = $this->request->get('position_id');
        $user_id = $user->id;
        $shop_id = $user->shop_id;
        $assignments = $this->request->get('assignments');
        $shift_name = $this->request->get('name');
        $work_day = $this->request->get('work_day');
        $is_OT = $this->request->get('is_OT');

        $shift_check = Shift::where(['shift_key' => $shift_key,'shop_id' => mongo_id($shop_id)])->first();
        if (!empty($shift_check)) {
            return $this->errorBadRequest('M?? ca ???? t???n t???i');
        }



        //tao time
        $time_begin = $this->request->get('time_begin');
        $time_end = $this->request->get('time_end');

        //Parse sang carbon
        $time_begin_client = Carbon::parse($time_begin);
        $time_end_client = Carbon::parse($time_end);
        //Check th???i gian ca l??m c?? tr??ng hay kh??ng
        $condition_time_begin =null;
        $listShift = Shift::where('shop_id','=',$shop_id)->get();
        foreach ($listShift as $shift) {

            //Check th???
            
            $time_begin_db = Carbon::parse($shift->time_begin);
            $time_end_db = Carbon::parse($shift->time_end);
                //so s??nh 
            $condition_time_begin = (($time_begin_client >= $time_begin_db) && ($time_begin_client <= $time_end_db));
            $condition_time_end = (($time_end_client >= $time_begin_db) && ($time_end_client <= $time_end_db));
            $condition = $condition_time_begin || $condition_time_end;
            if(($condition)){
                return $this->errorBadRequest('Th???i gian c???a ca ???? b??? tr??ng');
            }
            
        }

        // $depCheck = Dep::where(['_id' => mongo_id($dep_id)])->first();
        // if (empty($depCheck)) {
        //     return $this->errorBadRequest('Ch??a c?? ph??ng ban');
        // }
        // $branch_id = mongo_id($depCheck->branch_id);
        // $branchCheck = Branch::where(['_id' => $branch_id])->first();


        // T???o ca l???n
        $attributes = [
            'name' => $shift_name,
            'shop_id' => mongo_id($shop_id),
            'branch_ids' => $branch_ids,
            'dep_ids' => $dep_ids,
            'time_begin' => $time_begin,
            'time_end' => $time_end,
            'work_day' => $work_day,
            'is_OT' => $is_OT,
            'shift_key' => $shift_key,
            'assignments' => $assignments,
            
        ];
        $shift = $this->shiftRepository->create($attributes);


        //L???y danh s??ch user
        $user_list = User::where((['shop_id' => $shop_id]))->get();

        //T???o ca trong 1 th??ng
        $work_date_begin = Carbon::now();
        $work_date_end = Carbon::now()->addWeek(2)->endOfWeek();
        //Kho???ng th???I gian kh???i t???o ca
        $work_date = CarbonPeriod::create($work_date_begin, $work_date_end);


        //B??? c??c ng??y ko c?? ca 


        //T???o ca cho t???ng nh??n vi??n
        // $emp_shift = [];



        foreach ($user_list as $user) {
            foreach ($work_date as $day) {
                $dayOfWeek = $day->dayOfWeek;
                $user_id = $user['_id'];

                $weekMap = [
                    0 => 'SUN',
                    1 => 'MON',
                    2 => 'TUE',
                    3 => 'WED',
                    4 => 'THU',
                    5 => 'FRI',
                    6 => 'SAT',
                ];

                if ($assignments[$dayOfWeek]) {


                    $attributes = [
                        'shift_name' => $shift_name,
                        'user_id' => mongo_id($user_id),
                        'shift_id' => mongo_id($shift->_id),
                        'working_date' => $day,
                        'time_begin' => $time_begin,
                        'time_end' => $time_end,
                        'work_day' => $work_day,
                        'is_OT' => $is_OT,
                        'checkin_time' => null,
                        'checkout_time' => null,
                        'status' =>-1,
                        'dayOfWeek' => $weekMap[$dayOfWeek]
                    ];

                    $this->empshiftRepository->create($attributes);

                    // $emp_shift[] = $attributes;
                }
            }
        }

        // Empshift::insert($emp_shift);

        //tao ngay lam
        /*$work_date = CarbonPeriod::create($work_date_begin, $work_date_end);
        $data = [];

        foreach ($work_date as $row) {
            if ($row->isSunday() == false)
                $attributes = [
                    'shift_name' => $this->request->get('shift_name'),
                    'shop_id' => $shop_id,
                    'branch_id' => $branch_id,
                    'dep_id' => $dep_id,
                    'work_date' => $row,
                    'time_begin' => $time_begin,
                    'time_end' => $time_end,
                ];
            $data[] = $attributes;
            $shift[] = $this->shiftRepository->create($attributes);
        }*/
        return $this->successRequest($shift);
    }
    #endregion

    #region xoa ca l??m
    public function delShift()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id = $this->request->get('id');
        // Ki???m tra xem email ???? ???????c ????ng k?? tr?????c ???? ch??a
        $idCheck = Shift::where(['_id' => $id])->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Ca l??m kh??ng t???n t???i'));
        }

        // T???o shop tr?????c
        $idCheck->delete();



        return $this->successRequest();

        // return $this->successRequest($user->transform());
    }
    #endregion

    #region sua l??m
    public function editShift()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required',
            'shift_name' => 'required',
            'time_begin' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id = $this->request->get('id');
        // Ki???m tra xem ca l??m ???? ???????c ????ng k?? tr?????c ???? ch??a

        $idCheck = Shift::where(['_id' => $id])->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Ca l??m kh??ng t???n t???i'));
        }


        // l???y thu???c t??nh
        $attributes = [
            'shift_name' => $this->request->get('shift_name'),
            'time_begin' => $this->request->get('time_begin'),
            'time_end' => $this->request->get('time_end'),
        ];
        $shift = $this->shiftRepository->update($attributes, $id);



        return $this->successRequest($shift->transform());

        // return $this->successRequest($user->transform());
    }
    #endregion

    #region xem ca lam
    //     public function viewShift()
    //     {
    //         // Validate Data import.
    //         /*$validator = \Validator::make($this->request->all(), [
    //             'branchname'=>'required',
    //         ]);
    //         if ($validator->fails()) {
    //             return $this->errorBadRequest($validator->messages()->toArray());
    //         }

    //         $branchname=$this->request->get('branchname');
    //         // Ki???m tra xem email ???? ???????c ????ng k?? tr?????c ???? ch??a

    //         $branchCheck=Branch::where(['branchName'=>$branchname])->first();
    //         if(empty($branchCheck)) {
    //             return $this->errorBadRequest(trans('Chi nh??nh kh??ng c?? s???n'));
    //         }

    //         $branchid=mongo_id($branchCheck->_id);

    //         $dep = Dep::where(['branch_id'=>$branchid])->paginate();



    //         return $this->successRequest($dep);*/
    // //        $shift=$this->shiftRepository->findByField('work_date','Th??? 6, 20-12-2019');
    //         $shifts=Shift::all()->groupBy('work_date');
    //         $data=[

    //         ];
    //         foreach ($shifts as $shift){
    //             $data[]=$shift->transform();
    //         }



    //         return $this->successRequest($data);



    //         // return $this->successRequest($user->transform());
    //     }
    #endregion
    public  function listShift()
    {
        $user = $this->user();
        $shop_id = $user->shop_id;

        // $validator = \Validator::make($this->request->all(), [
        //     'dep_id'=>'required',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->messages()->toArray());
        // }
        // $dep_check=Dep::where(['_id'=>$this->request->get('dep_id')])->first();
        // $branch_id=$dep_check->branch_id;
        // $shifts=Shift::where(['dep_id'=>$this->request->get('dep_id'),'branch_id'=>$branch_id,'shop_id'=>$shop_id])->get();
        // // dd($shifts);
        $shifts = Shift::where(['shop_id' => $shop_id])->get();
        $data = [];
        foreach ($shifts as $shift) {
            $data[] = $shift->transform();
        }
        return $this->successRequest($data);
    }
}
