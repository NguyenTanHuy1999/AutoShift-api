<?php

namespace App\Http\Controllers\Api\V1;
use App\Api\Repositories\Contracts\BranchRepository;
use App\Api\Repositories\Contracts\DepRepository;
use App\Api\Repositories\Contracts\ShopRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\ShiftRepository;
use App\Api\Repositories\Contracts\EmpshiftRepository;
use App\Api\Repositories\Contracts\WifiConfigRepository;
use App\Api\Repositories\Contracts\HistoryRepository;
use App\Api\Repositories\Contracts\EmpClockRepository;
use App\Api\Repositories\Contracts\TimekeepConfigRepository;
use App\Api\Repositories\Contracts\PositionRepository;
use App\Api\Repositories\Contracts\GeneralRepository;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\PositionController;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Dep;
use App\Api\Entities\Branch;
use App\Api\Entities\User;
use App\Api\Entities\Shop;
use App\Api\Entities\EmpClock;
use App\Api\Entities\Shift;
use App\Api\Entities\Empshift;
use App\Api\Entities\Salary;
use App\Api\Entities\History;
use App\Api\Entities\WifiConfig;
use App\Api\Entities\TimekeepConfig;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\Yaml\Tests\B;

class GeneralController extends Controller
{
    protected $branchRepository;
    protected $userRepository;
    protected $shiftRepository;
    protected $wifiConfigRepository;
    protected $historyRepository;
    protected $empclockRepository;
    protected $timekeepConfigRepository;
    protected $positionRepository;
    protected $depRepository;
    protected $empshiftRepository;
    protected $shopRepository;
    protected $auth;

    protected $request;
    public function __construct(
        BranchRepository $branchRepository,
        WifiConfigRepository $wifiConfigRepository,
        EmpClockRepository $empClockRepository,
        DepRepository $depRepository,
        PositionRepository $positionRepository,
        UserRepository $userRepository,
        ShopRepository $shopRepository,
        ShiftRepository $shiftRepository,
        EmpshiftRepository $empshiftRepository,
        HistoryRepository $historyRepository,
        TimekeepConfigRepository $timekeepConfigRepository,
        AuthManager $auth,
        GeneralRepository $generalRepository,
        Request $request
    ) {
        $this->branchRepository = $branchRepository;
        $this->depRepository = $depRepository;
        $this->shopRepository = $shopRepository;
        $this->userRepository = $userRepository;
        $this->shiftRepository = $shiftRepository;
        $this->empshiftRepository = $empshiftRepository;
        $this->wifiConfigRepository = $wifiConfigRepository;
        $this->historyRepository = $historyRepository;
        $this->empclockRepository = $empClockRepository;
        $this->timekeepConfigRepository = $timekeepConfigRepository;
        $this->positionRepository = $positionRepository;
        $this->request = $request;
        $this->auth = $auth;
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
                    if (($listHistory_statistical_1[$i]["late_check_in"]) > 300 && ($listHistory_statistical_1[$i]["soon_check_out"]) > 300) {
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
                if (($listCheckOut[$i]["late_check_in"]) > 480) {
                    $late_check_in += 1;
                }
                if (($listCheckOut[$i]["soon_check_out"]) > 480) {
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
    //Thống kê danh sách đi muộn về sớm
    public function statistical_late_soon()
    {
        //Chọn ngày cần xem thống kê
        $date = Carbon::parse($this->request->get('date')); //Ngày client chọn
        $working_date = $date->startOfDay();
        $listCheckOut = History::where(['type' => 'check_out', 'working_date' => $working_date])->get();
        $list_late_soon = [];
        foreach ($listCheckOut as $listChecks) {
            if(($listChecks->late_check_in)>750){
                $data_late_check_in = [
                    'type' => 'late_check_in',
                    'user_id' => $listChecks->user_id,
                    'user_name' => $listChecks->user_name,
                    'shift_id' => $listChecks->shift_id,
                    'shift_name' => $listChecks->shift_name,
                    'shift_time' => $listChecks->shift_time,
                    'late_check_in' => $listChecks->late_check_in
                ];
                $list_late_soon[]=$data_late_check_in;
            }
            if(($listChecks->soon_check_out)>750){
                $data_soon_check_out = [
                    'type' => 'soon_check_out',
                    'user_id' => $listChecks->user_id,
                    'user_name' => $listChecks->user_name,
                    'shift_id' => $listChecks->shift_id,
                    'shift_name' => $listChecks->shift_name,
                    'shift_time' => $listChecks->shift_time,
                    'soon_check_out' => $listChecks->soon_check_out
                ];
                $list_late_soon[]=$data_soon_check_out;
            }
        }
        return $this->successRequest($list_late_soon);
    }

    public function fakedata()
    {
       $depList = [];
       $userList = [];
       $positionList =[];
       //$branchList =[];
       $shiftList =[];
       $empShiftList =[];
       $wifiList = [];
        //Fake shop 
        // Tạo shop trước
       $attributesShop = [
           'name' => 'TANHUY',
           'shop_name' => $this->request->get('shop_name'),
           'email' => $this->request->get('name'),
       ];
       $shop = $this->shopRepository->create($attributesShop);
       //Shop_id
       $shop_id = $shop['_id'];

       //User Admin

       $admin_info = [
           'name' => $shop['name'],
           'avatar' => 'http://192.168.1.3:8081/uploads/TanHuy.jpg',
           'email' => null,
           'position_id' => null,
           //'branch_id' => $branch_id,
           'dep_id' => null,
           'is_root' => 1,
           'is_admin' =>1,
           'phone_number' => '0358805114',
           //'timekeep_config' =>$timekeepConfigList,
           'basic_salary' =>20000000,
           'shop_id' => $shop_id,
           'sex' => 1,
           'birth' => '1999-11-07',
       ];
       $user = $this->userRepository->create($admin_info);
       $users = $user->transform();
       $userList[]=$users;
       
       //Fake branch bỏ branch
      // $branchListName = [ 'Cần Đước' ,'Cần Giuộc','Bến Lức','Đước Hòa','Mộc Hóa','Thạnh Hóa','Tân Trụ','Thủ Thừa','TP.Tân An'];
       //for ($i=0; $i <3; $i++) { 
       //    $random_keys=array_rand($branchListName);
       //    $attributesDep = [
       //        'name' => $branchListName[$random_keys],
       //        'address' => 'Long An',
       //        'shop_id' => $shop_id,
      //         'note' => $this->request->get('note'),
      //     ];
      //     $branch = $this->branchRepository->create($attributesDep);
      //     $branchList[]=$branch;
      // }
       //Fake dep
       $depListName = [ 'Accountant','Human Resource','Financial','Technical ','Business'];

       for ($i=0; $i <5 ; $i++) { 
          // $random_keys1=array_rand($branchList);
          // $branch_id = $branchList[$random_keys1]["_id"];
           $attributes = [
               'name' => $depListName[$i],
               //'branch_id' => $branch_id,
               'shop_id' => $shop_id,
               'note' => $this->request->get('note')
           ];
           $dep = $this->depRepository->create($attributes);
           $depList[]= $dep;
       }

       //Fake position
       $position_name = [ 'Accountanting manager','Human resources manager','Finance manager','Technical manager','Business manager'];

       for ($i=0; $i <5 ; $i++) { 
         
           $attributes = [
               'shop_id' => $shop_id,
               'position_name' => $position_name[$i],
           ];
           $position = $this->positionRepository->create($attributes);
           $positionList[]= $position;
       }

       //Fake timekeep_config
       $timekeep_name = 'Home';
       $timekeep_ssid = 'My Huyen';
       $timekeep_bssid = '44:fb:5a:91:d5:7a';
       $timekeep_long ='10.523153';
       $timekeep_lat = '106.716475';
       $timekeep_address = 'Long An';
       $timekeep_imageRequire = 'false';


      
       $wifi = [
           'ssid' =>$timekeep_ssid,
           'bssid' =>$timekeep_bssid
       ];
       $location =[
           'long' =>$timekeep_long,
           'lat' => $timekeep_lat,
           'address' =>$timekeep_address
       ];


       //$timekeeConfigCheck = TimekeepConfig::where(['wifi' => $wifi,'location'=>$location])->first();


       //if (!empty($timekeeConfigCheck)) {
       //    return $this->errorBadRequest('Timekeep_config đã được sử dụng');
       //}
       $attributes = [
           'name' => $timekeep_name,
           'wifi' =>$wifi,
           'location' =>$location,
           'imageRequire' =>$timekeep_imageRequire,
           'shop_id' =>$shop_id
       ];

       $timekeepConfig = $this->timekeepConfigRepository->create($attributes);
       $timekeepConfigList = $timekeepConfig->transform();

       //Fake user
       $userListName= ['Nguyễn Gia Bảo','Trần Tô Bảo','Nguyễn Hoàng Ca','Huỳnh Mai Chung','Nguyễn Đỗ Cường','Phan Thái Dương','Trần Ngọc Đại','Lê Hồng Đạo','Nguyễn Tiến Đạt','Trần Hồng Điệp','Nguyễn Văn Đức',
       'Đặng Hữu Đức','Nguyễn Hoàng Giang','Lê Trường Giảng','Đoàn Nhật Hào','Mai Chí Hải','Ngô Văn Hải','Trần Đình Hậu','Hoàng Thái Hòa','Phan Phú Huy','Trần Thị Mai'];
       
       $listPhone = ['0988585568','0916175566','0987898882','0912040325','0989965118','0904352749','0902210733','0934447788','0977891369','0983266986',
       '0912177345','0903220098','0976785816','0983109724','0983899956','0984652666','0942554498','0388403008','0985861886','0904629579','0983054815'];

       $listBirth = ['1999-11-07','1989-12-07','1993-02-07','1987-04-02','1998-10-17','1997-09-07','1991-01-01','1995-03-27','1996-04-12','1990-04-07',
       '1985-11-07','1986-12-02','1982-11-24','1979-02-01','1988-11-20','1987-10-19','1989-05-19','1989-08-04','1992-10-20','1996-11-20','1985-03-17'];

       for ($i=0; $i <count($userListName,COUNT_NORMAL) ; $i++) { 
           $random_keys=array_rand($depList);
           $dep_id = $depList[$random_keys]["_id"];
           $positionList_id =$positionList[$random_keys]["_id"];
           $basic_salary = rand(5000000,15000000);
           $userAttributes = [
               'name' => $userListName[$i],
               'avatar' => 'http://192.168.1.3:8081/uploads/TanHuy.jpg',
               'email' => 'admin@gmail.com',
               'position_id' => $positionList_id,
               //'branch_id' => $branch_id,
               'dep_id' => $dep_id,
               'is_root' => 0,
               'is_admin' =>rand(0,1),
               'phone_number' => $listPhone[$i],
               //'timekeep_config' =>$timekeepConfigList,
               'basic_salary' => $basic_salary,
               'shop_id' => $shop_id,
               'sex' => rand(0,1),
               'birth' => $listBirth[$i],
           ];
           $user = $this->userRepository->create($userAttributes);
           $users = $user->transform();
           $userList[]=$users;
       }
       //Fake Shift
       // Tạo ca lớn
       $shiftListName =['Ca Sáng','Ca Chiều'];
       $listTimeBegin =['8:00','13:30'];
       $listTimeEnd=['12:00','17:30'];
       $assignments=[
           false,
           true,
           true,
           true,
           true,
           true,
           false
       ];
       for ($i=0; $i <2 ; $i++) { 
           //$random_keys=array_rand($depList);
           //$dep_id = $depList[$random_keys]["_id"];
           //$random_keys1=array_rand($branchList);
           //$branch_id = $branchList[$random_keys1]["_id"];
           $attributes = [
               'name' => $shiftListName[$i],
               'shop_id' => $shop_id,
              // 'branch_ids' => $branch_id,
               //'dep_ids' => $dep_id,
               'time_begin' => $listTimeBegin[$i],
               'time_end' => $listTimeEnd[$i],
               'shift_key' => $shiftListName[$i],
               'assignments' => $assignments,
           ];
           $shift = $this->shiftRepository->create($attributes);
           $shiftList[]=$shift;
           //Tạo ca cho từng nhân viên
           //Tạo ca trong 1 năm
           
           $work_date_begin = Carbon::now()->startofYear();
           $work_date_end = Carbon::now()->endOfYear();
           //Khoảng thờI gian khởi tạo ca
           $work_date = CarbonPeriod::create($work_date_begin, $work_date_end);
           foreach ($userList as $user) {
               foreach ($work_date as $day) {
                   $dayOfWeek = $day->dayOfWeek;
                   $user_id = $user['id'];
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
                           'shift_name' => $shiftListName[$i],
                           'user_id' => $user_id,
                           'shift_id' => $shiftList[$i]["_id"],
                           'working_date' => $day,
                           'time_begin' => $listTimeBegin[$i],
                           'time_end' => $listTimeEnd[$i],
                           'checkin_time' => null,
                           'checkout_time' => null,
                           'dayOfWeek' => $weekMap[$dayOfWeek]
                       ];
                       $empShift=$this->empshiftRepository->create($attributes);
                       $empShiftList[]=$empShift;
                   }
               }
           }
       }
       //Fake Wifi bỏ phần này
      // $listNameWife =['Wifi Company','Wifi School'];
       //$listSsid =['My Company','My School'];
      // for ($i=0; $i <2 ; $i++) { 
           //$random_keys=array_rand($depList);
          // $dep_id = $depList[$random_keys]["_id"];
          // $branch_id =$depList[$random_keys]["branch_id"];
          // $attributes = [
               //'name' => $listNameWife[$i],
              // 'bssid' => '44:fb:5a:91:d5:7a',
              // 'ssid' => $listSsid[$i],
              // 'branch_id' => $branch_id,
              // 'dep_id' => $dep_id,
              // 'shop_id' => $shop_id
           //];
          // $wifi = $this->wifiConfigRepository->create($attributes);
           //$wifiList[]=$wifi;
      //}



       //Fake EmpClock ca sáng
       //shift_id ca sáng
       $shift_id = $shiftList[0]["_id"];
       $shift_name = $shiftList[0]["name"];
       $work_date_begin = Carbon::now()->startofYear();
       $work_date_end = Carbon::now();
       //Khoảng thờI gian khởi tạo ca
       $work_date = CarbonPeriod::create($work_date_begin, $work_date_end);
       foreach ($userList as $user) {
           foreach ($work_date as $day) {
               $dayOfWeek = $day->dayOfWeek;
               if ($assignments[$dayOfWeek]) {
                   $time_check = $day->addHour(7)->addMinute(55)->addMinutes(rand(1,20));
                   $user_id = $user['id'];
                   $user_name =$user['name'];
                   $emp_shift = Empshift::where('user_id','=',$user_id)->where('shift_id','=',$shift_id)->where('working_date','<=',$day)->get();
                   //Các biến random
                   $late_check_in = rand(1,600);
                   $soon_check_out = rand(1,600);
                   $real_working_hours =14400- ($late_check_in +  $soon_check_out );
                   
                   $i = count($emp_shift,COUNT_NORMAL);
                   $working_date = $emp_shift[$i-1]["working_date"];
                   // dd($working_date);
                   $emp_shift_id = $emp_shift[$i-1]["_id"];
                  
                   //History check in
                   $data = [
                       'user_id' => $user_id,
                       'user_name' => $user_name,
                       'working_date' => $working_date,
                       'emp_shift_id' => $emp_shift_id,
                       'shift_name' => $shift_name,
                       'shift_time' => '8:00-12:00',
                       'shift_id' => $shift_id,
                       'time_check' => $time_check,
                       'status' => 1,
                       'timekeep_config' => $timekeepConfigList,
                       'type' => 'check_in'
                   ];
                   $emp_history = $this->historyRepository->create($data);
                   //dd($emp_history);
                   //EmpClock check in
                   $attribute = [
                       'user_id' => $user_id,
                       'emp_shift_id' => $emp_shift_id,
                       'shift_id' => $shift_id,
                       'time_in' => $time_check,
                       'time_out' => null,
                       'status' => 1,
                       'isCheckOut' => false,
                   ];
                   //EmpClock check out
                   $emp_clock = $this->empclockRepository->create($attribute);
                   $clock_check =EmpClock::where(['user_id' => $user_id,'isCheckOut' => false])->first();
                   $attribute1 = [
                       'time_out' => $time_check->addHour(4)->addMinutes(5)->subMinutes(rand(1,20)),
                       'status' => 0,
                       'isCheckOut' => true,
                   ];
                   $emp_clock1 = $this->empclockRepository->update($attribute1, $clock_check->_id);
                   //History check out
                   $data1 = [
                       'user_id' => $user_id,
                       'user_name' => $user_name,
                       'working_date' => $working_date,
                       'emp_shift_id' => $emp_shift_id,
                       'shift_id' => $shift_id,
                       'shift_name' => $shift_name,
                       'shift_time' => '8:00-12:00',
                       'time_check' => $time_check,
                       'status' => 0,
                       'type' => 'check_out',
                       'real_working_hours'=>$real_working_hours,
                       'late_check_in' =>$late_check_in,
                       'soon_check_out' =>$soon_check_out,
                       'month' =>$day->month,
                       'year' =>$day->year,
                   ];
                   $emp_history = $this->historyRepository->create($data1);
                   //djson($emp_history);
               }
           }
       }

       //Fake EmpClock ca chiều
       //shift_id Ca chiều
       $shift_id1 = $shiftList[1]["_id"];
       $shift_name1 = $shiftList[1]["name"];
       foreach ($userList as $user) {
           foreach ($work_date as $day) {
               $dayOfWeek = $day->dayOfWeek;
               if ($assignments[$dayOfWeek]) {
                   $time_check = $day->addHours(13)->addMinute(25)->addMinutes(rand(1,20));
                   //dd($time_check);
                   $user_id = $user['id'];
                   $user_name =$user['name'];
                   $emp_shift = Empshift::where('user_id','=',$user_id)->where('shift_id','=',$shift_id1)->where('working_date','<=',$day)->get();
                   //Các biến random
                   $late_check_in = rand(1,600);
                   $soon_check_out = rand(1,600);
                   $real_working_hours =14400- ($late_check_in +  $soon_check_out );

                   $i = count($emp_shift,COUNT_NORMAL);
                   $working_date = $emp_shift[$i-1]["working_date"];
                   // dd($working_date);
                   $emp_shift_id = $emp_shift[$i-1]["_id"];
                   //History check in
                   $data = [
                       'user_id' => $user_id,
                       'user_name' => $user_name,
                       'working_date' => $working_date,
                       'emp_shift_id' => $emp_shift_id,
                       'shift_name' => $shift_name1,
                       'shift_time' => '13:30-17:30',
                       'shift_id' => $shift_id1,
                       'time_check' => $time_check,
                       'status' => 1,
                       'timekeep_config' => $timekeepConfigList,
                       'type' => 'check_in'
                   ];
                   $emp_history = $this->historyRepository->create($data);
       
                   //EmpClock check in
                   $attribute = [
                       'user_id' => $user_id,
                       'emp_shift_id' => $emp_shift_id,
                       'shift_id' => $shift_id1,
                       'time_in' => $time_check,
                       'time_out' => null,
                       'status' => 1,
                       'isCheckOut' => false,
                   ];
                   //EmpClock check out
                   $emp_clock = $this->empclockRepository->create($attribute);
                   $clock_check =EmpClock::where(['user_id' => $user_id,'isCheckOut' => false])->first();
                   $attribute1 = [
                       'time_out' => $time_check->addHour(4)->addMinutes(5)->subMinutes(rand(1,30)),
                       'status' => 0,
                       'isCheckOut' => true,
                   ];
                   $emp_clock1 = $this->empclockRepository->update($attribute1, $clock_check->_id);
                   //History check out
                   $data1 = [
                       'user_id' => $user_id,
                       'user_name' => $user_name,
                       'working_date' => $working_date,
                       'emp_shift_id' => $emp_shift_id,
                       'shift_id' => $shift_id1,
                       'shift_name' => $shift_name1,
                       'shift_time' => '13:30-17:30',
                       'time_check' => $day,
                       'status' => 0,
                       'type' => 'check_out',
                       'real_working_hours'=>$real_working_hours,
                       'late_check_in' =>$late_check_in,
                       'soon_check_out' =>$soon_check_out,
                       'month' =>$day->month,
                       'year' =>$day->year,
                   ];
                   $emp_history = $this->historyRepository->create($data1);
               }
           }
       }
       return $this->successRequest('Fake dữ liệu thành công!!');
      
   }//end Fake data
 }