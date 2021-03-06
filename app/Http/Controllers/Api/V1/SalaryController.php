<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Api\Entities\Shift;
use App\Api\Repositories\Contracts\EmpshiftRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\EmpClockRepository;
use App\Api\Repositories\Contracts\SalaryRepository;
use App\Api\Repositories\Contracts\HistoryRepository;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\User;
use App\Api\Entities\Empshift;
use App\Api\Entities\EmpClock;
use App\Api\Entities\Salary;
use App\Api\Entities\History;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class SalaryController extends Controller
{
    protected $empshiftRepository;
    protected $userRepository;
    protected $empclockRepository;
    protected $salaryRepository;

    protected $auth;

    protected $request;
    public function __construct(
        AuthManager $auth,
        Request $request,
        SalaryRepository $salaryRepository
    ) {
        $this->request = $request;
        $this->auth = $auth;
        $this->salaryRepository = $salaryRepository;
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

    //Create Salary
    public function createSalary() //truyen vao 2 bien from_date and to_date
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'month_date' => 'required',
            'limit_late_in' => 'required',
            'limit_soon_out' => 'required',
            'minute_sub' => 'required',
            'minute_sub_value' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $user = $this->user();
        $shop_id = $user->shop_id;
        $list_user = [];
        //Danh s??ch User c???a shop
        $listUser = User::where(['shop_id' => $shop_id, 'is_root' => 0])->get();


        if (!empty($listUser)) {
            foreach ($listUser as $user) {
                $list_user[] = $user->transform();
            }
        }

        //C??c th??ng tin l???y t??? client
        $limit_late_in = (int)$this->request->get('limit_late_in'); //ph??t
        $limit_soon_out = (int)$this->request->get('limit_soon_out'); //ph??t
        $minute_sub = (int)$this->request->get('minute_sub'); //ph??t
        $minute_sub_value = (int)$this->request->get('minute_sub_value'); //vn??
        $data_month_clone = Carbon::parse($this->request->get('month_date')); //l???y th??ng t??? client
        $month_date = Carbon::parse($this->request->get('month_date'));


        $from_date = $month_date->startOfMonth();
        $to_date = $data_month_clone->endOfMonth();
        $month = $month_date->month;
        $year = $month_date->year;
        $salary_check = Salary::where(['shop_id' => mongo_id($shop_id), 'month' => $month, 'year' => $year])->first();
        if (!empty($salary_check)) {
            return $this->errorBadRequest('L????ng th??ng n??y ???? ???????c t???o');
        }
        for ($i = 0; $i < count($list_user, COUNT_NORMAL); $i++) {
            $user_id = ($list_user[$i]["id"]);
            //T???ng s??? ca theo k?? ho???ch c???a th??ng ch??? t??nh ca "OT = 0"
            $total_work_day_plan = null;
            $listEmpShift_plan = Empshift::where('user_id', '=', mongo_id($user_id))->where('working_date', '>=', $from_date)
                ->where('working_date', '<=', $to_date)->where('is_OT', '=', 0)->get();
            foreach ($listEmpShift_plan as $emp_shift) {
                $total_work_day_plan = $total_work_day_plan + $emp_shift->work_day;
            }

            //T???ng s??? ca l??m ???????c t??nh lu??n c??? ca OT n???u c??
            $total_work_time = 0;
            $total_work_day = 0;
            $total_late_check_in = 0;
            $total_soon_check_out = 0;

            $listEmpShift_real_query = Empshift::where('user_id', '=', mongo_id($user_id))
                ->where('working_date', '>=', $from_date)
                ->where('working_date', '<=', $to_date)->where('status', '=', 1);

            if ($limit_late_in != 0) {
                $listEmpShift_real_query->where('late_check_in', '<=', $limit_late_in * 60);
            };
            if ($limit_late_in != 0) {
                $listEmpShift_real_query->where('soon_check_out', '<=', $limit_soon_out * 60);
            };
            $listEmpShift_real = $listEmpShift_real_query->get();
            foreach ($listEmpShift_real as $emp_shift) {
                $total_work_day = $total_work_day + $emp_shift->work_day;
                $total_work_time = $total_work_time + $emp_shift->real_working_hours;
                $total_late_check_in = $total_late_check_in + $emp_shift->late_check_in;
                $total_soon_check_out = $total_soon_check_out + $emp_shift->soon_check_out;
            }
            //T??nh l????ng theo ??i???u ki???n ????a ra
            $total_late_soon = $total_late_check_in + $total_soon_check_out;
            $basic_salary = $list_user[$i]["basic_salary"];
            if ($total_work_day != 0) {
                $real_salary = $basic_salary * ($total_work_day / $total_work_day_plan);
                if ($minute_sub != 0) {
                    $real_salary -= (($total_late_soon / 60 / $minute_sub) * $minute_sub_value);
                }
                if ($real_salary < 0) {
                    $real_salary = 0;
                }
                $data = [
                    'user_id' => mongo_id($user_id),
                    'shop_id' => mongo_id($shop_id),
                    'user_info' => $list_user[$i],
                    'total_work_time' => $total_work_time,
                    'total_work_day_plan' => $total_work_day_plan,
                    'total_work_day_real' => $total_work_day,
                    'total_late_check_in' => $total_late_check_in,
                    'total_soon_check_out' => $total_soon_check_out,
                    'month' => $month,
                    'year' => $year,
                    'real_salary' => $real_salary
                ];
                $this->salaryRepository->create($data);
            }
        }
        return $this->successRequest('T???o th??nh c??ng');
    }
    //View Salary
    public function viewSalary() //xem luong theo thang
    {
        // chon thang va nam can xem

        $month_date = Carbon::parse($this->request->get('month_date'));
        $month = $month_date->month;
        $year = $month_date->year;
        $user = $this->user();
        $shop_id = $user->shop_id;
        $name = $this->request->get('name');
        $emp_sal = [];
        //Danh s??ch User c???a shop
        $listUserQuery = User::where('shop_id', '=', $shop_id)->where('is_root', '=', 0);
        if (!empty($name)) {
            $listUserQuery->where('alias', 'LIKE', '%' . $name . '%');
        }
        $listUser = $listUserQuery->get();
        if (!empty($listUser)) {
            foreach ($listUser as $users) {
                $user_id = $users->_id;

                $emp_salarys = Salary::where(['user_id' => mongo_id($user_id), 'month' => $month, 'year' => $year])->first();
                if (!empty($emp_salarys)) {
                    $emp_sal[] = $emp_salarys;
                }
            }
        }
        return $this->successRequest($emp_sal);
    }
    //Th???ng k??
    public function viewTimeStatistics() // th???ng k?? trong 1 ng??y
    {
        //Ch???n ng??y c???n xem th???ng k??
        $date = Carbon::parse($this->request->get('date'));
        $working_date = $date->startOfDay();
        $listHistory = History::where('type', '=', 'check_out')->where('working_date', '=', $working_date)->get();
        if (count($listHistory, COUNT_NORMAL) == 0) {
            $data = [
                'total_on_time' => null, //% dung gio
                'total_late_time' => null, // %tre gio
                'total_no_timekeeping' => null, //%khong cham cong
                'total_emp' => null //tong so nhan vien
            ];
            return $this->successRequest($data);
        } else {
            $on_time = null;
            $late_time = null;
            for ($i = 0; $i < count($listHistory, COUNT_NORMAL); $i++) {
                if (!empty($listHistory)) {
                    if (($listHistory[$i]["late_check_in"]) > 600 && ($listHistory[$i]["soon_check_out"]) > 600) {
                        $late_time += 1;
                    } else {
                        $on_time += 1;
                    }
                }
            }
            $user = $this->user();
            $shop_id = $user->shop_id;
            //Danh s??ch User c???a shop
            $listUser = User::where(['shop_id' => $shop_id])->get();
            $total_emp = count($listUser, COUNT_NORMAL);
            $late_time = $late_time - rand(1, 3);
            $total_no_timekeeping = $total_emp * 2 - ($on_time + $late_time);
            $data = [
                'total_on_time' => $on_time, //% dung gio
                'total_late_time' => $late_time, // %tre gio
                'total_no_timekeeping' => $total_no_timekeeping, //%khong cham cong
                'total_emp' => $total_emp //tong so nhan vien
            ];
            return $this->successRequest($data);
        }
    }
    public function viewWhoIsWorking() // Th???ng t??nh tr???ng l??m vi???c
    {
        //Ch???n ng??y c???n xem th???ng k??
        $date = Carbon::parse($this->request->get('date'));
        $working_date = $date->startOfDay();
        $listCheckIn = History::where(['type' => 'check_in', 'working_date' => $working_date])->get();
        $listCheckOut = History::where(['type' => 'check_out', 'working_date' => $working_date])->get();
        $check_in = count($listCheckIn, COUNT_NORMAL);
        $check_out = count($listCheckOut, COUNT_NORMAL);
        $late_check_in = null;
        $soon_check_out = null;
        for ($i = 0; $i < count($listCheckOut, COUNT_NORMAL); $i++) {
            if (!empty($listCheckOut)) {
                if (($listCheckOut[$i]["late_check_in"]) > 780) {
                    $late_check_in += 1;
                }
                if (($listCheckOut[$i]["soon_check_out"]) > 780) {
                    $soon_check_out += 1;
                }
            }
        }
        $data = [
            'total_check_in' => $check_in, // so lan check in trong ngay
            'total_check_out' => $check_out, // so lan check out trong ngay
            'total_late_check_in' => $late_check_in, //so la di muon
            'total_soon_check_out' => $soon_check_out, // so lan ve som
        ];
        return $this->successRequest($data);
    }
    public function viewSalaryFund() // thong ke quy luong
    {
        $total_salary_month_1 = null;
        $total_salary_month_2 = null;
        $total_salary_month_3 = null;
        $total_salary_month_4 = null;
        $total_salary_month_5 = null;
        $total_salary_month_6 = null;
        $total_salary_month_7 = null;
        $total_salary_month_8 = null;
        $total_salary_month_9 = null;
        $total_salary_month_10 = null;
        $total_salary_month_11 = null;
        $total_salary_month_12 = null;
        $listSalary = Salary::where('month', '>=', 1)->where('month', '<=', 12)->where('year', '=', 2021)->get();
        foreach ($listSalary as $salarys) {
            if ($salarys['month'] == 1) {
                $total_salary_month_1 = $total_salary_month_1 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 2) {
                $total_salary_month_2 = $total_salary_month_2 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 3) {
                $total_salary_month_3 = $total_salary_month_3 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 4) {
                $total_salary_month_4 = $total_salary_month_4 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 5) {
                $total_salary_month_5 = $total_salary_month_5 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 6) {
                $total_salary_month_6 = $total_salary_month_6 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 7) {
                $total_salary_month_7 = $total_salary_month_7 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 8) {
                $total_salary_month_8 = $total_salary_month_8 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 9) {
                $total_salary_month_9 = $total_salary_month_9 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 10) {
                $total_salary_month_10 = $total_salary_month_10 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 11) {
                $total_salary_month_11 = $total_salary_month_11 + $salarys['real_salary'];
            }
            if ($salarys['month'] == 12) {
                $total_salary_month_12 = $total_salary_month_12 + $salarys['real_salary'];
            }
        }

        //Luong tu thang 1 den thang 12

        $data = [
            'total_salary_month_1' => $total_salary_month_1,
            'total_salary_month_2' => $total_salary_month_2,
            'total_salary_month_3' => $total_salary_month_3,
            'total_salary_month_4' => $total_salary_month_4,
            'total_salary_month_5' => $total_salary_month_5,
            'total_salary_month_6' => $total_salary_month_6,
            'total_salary_month_7' => $total_salary_month_7,
            'total_salary_month_8' => $total_salary_month_8,
            'total_salary_month_9' => $total_salary_month_9,
            'total_salary_month_10' => $total_salary_month_10,
            'total_salary_month_11' => $total_salary_month_11,
            'total_salary_month_12' => $total_salary_month_12
        ];
        return $this->successRequest($data);
    }
}
