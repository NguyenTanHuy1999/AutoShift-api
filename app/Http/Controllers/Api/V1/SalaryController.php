<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Api\Entities\Shift;
use App\Api\Repositories\Contracts\EmpshiftRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\EmpClockRepository;
use App\Api\Repositories\Contracts\SalaryRepository;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\User;
use App\Api\Entities\Empshift;
use App\Api\Entities\EmpClock;
use App\Api\Entities\Salary;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class SalaryController extends Controller
{
    protected $empshiftRepository;
    protected  $userRepository;
    protected $empclockRepository;
    protected $salaryRepository;

    protected $auth;

    protected $request;
    public function __construct(  AuthManager $auth,
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

    




    public  function viewSalary()
    {
//        Log::debug('test1');
        $user=$this->user();

        $salarys=Salary::where(['user_id'=>($user->_id)])->get();
        //  $salarys=Salary::select('user_id','SUM(work_time) as work_time','SUM(salary) as salary')->get();
        // $salarys=Salary::select('user_id','work_time','salary')->get();
        // dd($salarys);
        //Luu cong va luong vao mang user_sal
        $user_sal=[];
        foreach($salarys as $salary){

            $user_sal[]=$salary->transform();

        }
        //tao mang user de tinh tong cong va luong
        $user=[
            "user_id"=>$user->_id,
            "work_time"=>NULL,
            "salary"=>NULL
        ];
        //ham tinh tong cong va luong
        $sum_time=0;
        $sum_sal=0;
        for($i=0;$i<count($user_sal,COUNT_NORMAL);$i++){
            $sum_time=$sum_time+($user_sal[$i]["work_time"]);
            $sum_sal=$sum_sal+($user_sal[$i]["salary"]);
            // dd($sum_sal);
        }
        //push tong cong va luong vao user
        $user["work_time"]=$sum_time;
        $user["salary"]=$sum_sal;
        // dd($user);
        return $this->successRequest($user);
    }
    //Check khoảng thời gian check in và check out với thời gian trong bảng timeshift
}
