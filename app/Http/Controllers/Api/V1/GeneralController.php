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
use App\Api\Repositories\Contracts\GeneralRepository;

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
use App\Api\Entities\General;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class GeneralController extends Controller
{
    protected $empshiftRepository;
    protected $userRepository;
    protected $empclockRepository;
    protected $salaryRepository;
    protected $generalRepository;
    protected $auth;

    protected $request;
    public function __construct(
        AuthManager $auth,
        Request $request,
        SalaryRepository $salaryRepository,
        GeneralRepository $generalRepository
    ) {
        $this->request = $request;
        $this->auth = $auth;
        $this->salaryRepository = $salaryRepository;
        $this->generalRepository = $generalRepository;
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
    public function statistical(){
        //Thống kê thứ 1: Theo % đúng giờ, trễ giờ, không chấm công và  tổng số nhân viên 
        //Chọn ngày cần xem thống kê
        $date = Carbon::parse($this->request->get('date')); //Ngày client chọn
        $working_date = $date->startOfDay();
        $listHistory_statistical_1 = History::where('type', '=', 'check_out')->where('working_date', '=', $working_date)->get();
        if (count($listHistory_statistical_1, COUNT_NORMAL)==0) {
            $data_statistical_time = [
                'total_on_time' => null, //% dung gio
                'total_late_time' => null, // %tre gio
                'total_no_timekeeping' => null, //%khong cham cong
                'total_emp' => null //tong so nhan vien
            ];
        } else {
            $on_time = null;
            $late_time = null;
            for ($i = 0; $i < count($listHistory_statistical_1, COUNT_NORMAL); $i++) {
                if (!empty($listHistory_statistical_1)) {
                    if (($listHistory_statistical_1[$i]["late_check_in"]) > 600 && ($listHistory_statistical_1[$i]["soon_check_out"]) > 600) {
                        $late_time += 1;
                    } else {
                        $on_time += 1;
                    }
                }
            }
            $user = $this->user();
            $shop_id = $user->shop_id;
            //Danh sách User của shop
            $listUser = User::where(['shop_id' => $shop_id])->get();
            $total_emp = count($listUser, COUNT_NORMAL);
            $late_time = $late_time - rand(1, 3);
            $total_no_timekeeping = $total_emp * 2 - ($on_time + $late_time);
            $data_statistical_time = [
                'total_on_time' => $on_time, //% dung gio
                'total_late_time' => $late_time, // %tre gio
                'total_no_timekeeping' => $total_no_timekeeping, //%khong cham cong
                'total_emp' => $total_emp //tong so nhan vien
            ];
        }
        //Thống kê thứ 2: Tình trạng làm việc vẫn chọn 1 ngày nhất định để thống kê
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
        $data_statistical_who_is_working = [
            'total_check_in' => $check_in, // so lan check in trong ngay
            'total_check_out' => $check_out, // so lan check out trong ngay
            'total_late_check_in' => $late_check_in, //so la di muon
            'total_soon_check_out' => $soon_check_out, // so lan ve som
        ];
        //Thống kê thứ 3: Thống kê quỷ lương theo tháng
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

        $data_statistical_salary_fund = [
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
        //kết quả cuối cùng
        $data =[
            'data_statistical_time' => $data_statistical_time,
            'data_statistical_who_is_working'=> $data_statistical_who_is_working,
            'data_statistical_salary_fund'=> $data_statistical_salary_fund
        ];
        return $this->successRequest($data);
    }
 }