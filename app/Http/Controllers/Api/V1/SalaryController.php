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
            

            $real_salary = 6000000*($total_work_day/$total_work_read);//Lương cố định sẽ được tạo cùng với user

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
        $from_date =Carbon::parse('2021-01-01 00:00:00');//Người dùng chọn ngày
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
    //Check khoảng thời gian check in và check out với thời gian trong bảng timeshift
}
