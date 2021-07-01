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
        SalaryRepository $salaryRepository)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->salaryRepository=$salaryRepository;
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
    public function createSalary()
    {   
        
        $user = $this->user();
        $shop_id = $user->shop_id;
        $list_user = [];
        //Danh sách User của shop
        $listUser = User::where(['shop_id' => $shop_id])->get();
       
       
        if (!empty($listUser)) {
            foreach ($listUser as $user) {
                $list_user[] = $user->transform();
            }
        }
        
        for($i=0;$i<count($list_user,COUNT_NORMAL);$i++){
            $user_id = ($list_user[$i]["id"]);
            //List Empshift trong thang
            $from_date =Carbon::parse('2021-01-01 00:00:00');//Người dùng chọn ngày
            $to_date = Carbon::parse('2021-01-31 23:00:00');//Người dùng chọn ngày
            $month = $from_date ->month;
            $year = $from_date ->year;
            $listEmpShift = Empshift::where('user_id','=',$user_id)->where('working_date','>=',$from_date)->where('working_date','<=',$to_date)->get();
            $total_work_read = count($listEmpShift,COUNT_NORMAL)/2;
            $listHistory = History::where(['user_id' => $user_id,'type'=>'check_out','month'=>$month,'year'=>$year])->get();
            $total_work_time=0;
            $total_work_day=0;
            $total_late_check_in=0;
            $total_soon_check_out=0;
            for($j=0;$j<count($listHistory,COUNT_NORMAL);$j++){
                $total_work_time = $total_work_time + ($listHistory[$j]["real_working_hours"]);
                $total_work_day = $total_work_day + 0.5;
                $total_late_check_in = $total_late_check_in + ($listHistory[$j]["late_check_in"]);
                $total_soon_check_out = $total_soon_check_out + ($listHistory[$j]["soon_check_out"]);    
            }
            
            $basic_salary = rand(5000000,10000000);
            $real_salary = $basic_salary*($total_work_day/$total_work_read);//Lương cố định sẽ được tạo cùng với user

            $data = [
                'user_id'=> $user_id,
                'total_work_time'=>$total_work_time,
                'total_work_day'=>$total_work_day,
                'total_late_check_in'=>$total_late_check_in,
                'total_soon_check_out'=>$total_soon_check_out,
                'month'=>$month,
                'year' =>$year,
                'real_salary'=>$real_salary
            ];
            $emp_salary = $this->salaryRepository->create($data);
        }
        return $this->successRequest($emp_salary->transform());
    }  
    //View Salary
    public  function viewSalary()
    {   
        //Chọn mốc thời gian để xem dánh sách lương
        $from_date = Carbon::parse('2021-01-01 00:00:00');//Người dùng chọn ngày
        $to_date = Carbon::parse('2021-01-31 23:00:00');//Người dùng chọn ngày
        $month = $from_date ->month;
        $year = $from_date ->year;


        $user = $this->user();
        $shop_id = $user->shop_id;
        $list_user = [];
        //Danh sách User của shop
        $listUser = User::where(['shop_id' => $shop_id])->get();
        foreach ($listUser as $users) {
            $user_id = $users->_id;
            $emp_salarys=Salary::where(['user_id'=>$user_id,'month'=>$month,'year'=>$year])->get();
            $emp_sal[]= $emp_salarys;
            
        }
        return $this->successRequest($emp_sal);
    }
    //Thống kê
    public  function viewTimeStatistics() // Thong ke thoi gian
    {
        $date = Carbon::parse('2021-02-15 10:00:00');//Ngay nguoi dung chon
        $working_date = $date ->startOfDay();
        $listHistory = History::where(['type'=>'check_out','working_date'=>$working_date])->get();
        $on_time = null;
        $late_time = null;
        for ($i=0; $i <count($listHistory,COUNT_NORMAL) ; $i++) { 
            if (!empty($listHistory)){
                if(($listHistory[$i]["late_check_in"])>600 && ($listHistory[$i]["soon_check_out"])>600){
                    $late_time += 1;
                }else{
                    $on_time += 1;
                }
            }
        }
        $user = $this->user();
        $shop_id = $user->shop_id;
        //Danh sách User của shop
        $listUser = User::where(['shop_id' => $shop_id])->get();
        $total_emp = count($listUser,COUNT_NORMAL);
        $late_time = $late_time -rand(1,3);
        $total_no_timekeeping = $total_emp*2-($on_time+$late_time);
        $data =[
            'total_on_time' => $on_time, //% dung gio
            'total_late_time' => $late_time, // %tre gio
            'total_no_timekeeping' =>$total_no_timekeeping, //%khong cham cong
            'total_emp'=> $total_emp //tong so nhan vien
        ];
        return $this->successRequest($data);
    }
    public function viewWhoIsWorking() // thong ke so ng lam viec
    {
        $date = Carbon::parse('2021-02-17 10:00:00');//Ngay nguoi dung chon
        $working_date = $date ->startOfDay();
        $listCheckIn= History::where(['type'=>'check_in','working_date'=>$working_date])->get();
        $listCheckOut = History::where(['type'=>'check_out','working_date'=>$working_date])->get();
        $check_in = count($listCheckIn,COUNT_NORMAL);
        $check_out =count($listCheckOut,COUNT_NORMAL);
        $late_check_in = null;
        $soon_check_out =null;
        for ($i=0; $i <count($listCheckOut,COUNT_NORMAL) ; $i++) { 
            if (!empty($listCheckOut)){
                if(($listCheckOut[$i]["late_check_in"])>780){
                    $late_check_in += 1;
                }
                if(($listCheckOut[$i]["soon_check_out"])>780){
                    $soon_check_out +=1;
                }
            }
        }
        $data =[
            'total_check_in' =>$check_in, // so lan check in trong ngay
            'total_check_out' =>$check_out, // so lan check out trong ngay
            'total_late_check_in' =>$late_check_in, //so la di muon
            'total_soon_check_out' =>$soon_check_out, // so lan ve som
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
        $listSalary=Salary::where('month','>=',1)->where('month','<=',12)->where('year','=',2021)->get();
        foreach ($listSalary as $salarys) {
            if($salarys['month']==1){
                $total_salary_month_1 = $total_salary_month_1 + $salarys['real_salary'];
            }
            if($salarys['month']==2){
                $total_salary_month_2 = $total_salary_month_2 + $salarys['real_salary'];
            }
            if($salarys['month']==3){
                $total_salary_month_3 = $total_salary_month_3 + $salarys['real_salary'];
            }    
            if($salarys['month']==4){
                $total_salary_month_4 = $total_salary_month_4 + $salarys['real_salary'];
            }    
            if($salarys['month']==5){
                $total_salary_month_5 = $total_salary_month_5 + $salarys['real_salary'];
            }    
            if($salarys['month']==6){
                $total_salary_month_6 = $total_salary_month_6 + $salarys['real_salary'];
            }    
            if($salarys['month']==7){
                $total_salary_month_7 = $total_salary_month_7 + $salarys['real_salary'];
            }    
            if($salarys['month']==8){
                $total_salary_month_8 = $total_salary_month_8 + $salarys['real_salary'];
            }    
            if($salarys['month']==9){
                $total_salary_month_9 = $total_salary_month_9 + $salarys['real_salary'];
            }    
            if($salarys['month']==10){
                $total_salary_month_10 = $total_salary_month_10 + $salarys['real_salary'];
            }    
            if($salarys['month']==11){
                $total_salary_month_11 = $total_salary_month_11 + $salarys['real_salary'];
            }    
            if($salarys['month']==12){
                $total_salary_month_12 = $total_salary_month_12 + $salarys['real_salary'];
            }                  
        }
        //Luong tu thang 1 den thang 12
     
        $data=[
            'total_salary_month_1'=>$total_salary_month_1,
            'total_salary_month_2'=>$total_salary_month_2,
            'total_salary_month_3'=>$total_salary_month_3,
            'total_salary_month_4'=>$total_salary_month_4,
            'total_salary_month_5'=>$total_salary_month_5,
            'total_salary_month_6'=>$total_salary_month_6,
            'total_salary_month_7'=>$total_salary_month_7,
            'total_salary_month_8'=>$total_salary_month_8,
            'total_salary_month_9'=>$total_salary_month_9,
            'total_salary_month_10'=>$total_salary_month_10,
            'total_salary_month_11'=>$total_salary_month_11,
            'total_salary_month_12'=>$total_salary_month_12
        ];
        return $this->successRequest($data);
    }
}
