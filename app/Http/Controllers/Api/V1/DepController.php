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

class DepController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $branchRepository;
    protected $userRepository;
    protected $shiftRepository;
    protected $wifiConfigRepository;
    protected $historyRepository;
    protected $empclockRepository;
    protected $timekeepConfigRepository;

    /**
     * @var ShopRepository
     */
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
        UserRepository $userRepository,
        ShopRepository $shopRepository,
        ShiftRepository $shiftRepository,
        EmpshiftRepository $empshiftRepository,
        HistoryRepository $historyRepository,
        TimekeepConfigRepository $timekeepConfigRepository,
        AuthManager $auth,
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

     public function fakedata()
     {
        $depList = [];
        $userList = [];
        $branchList =[];
        $shiftList =[];
        $empShiftList =[];
        $wifiList = [];
        $timekeepConfigList =[];
         //Fake shop 
         // Tạo shop trước
        $attributesShop = [
            'name' => 'DUYKHANH',
            'shop_name' => $this->request->get('shop_name'),
            'email' => $this->request->get('name'),
        ];
        $shop = $this->shopRepository->create($attributesShop);
        //Shop_id
        $shop_id = $shop['_id'];
        //Fake branch
        $branchListName = [ 'Cần Đước' ,'Cần Giuộc','Bến Lức','Đước Hòa','Mộc Hóa','Thạnh Hóa','Tân Trụ','Thủ Thừa','TP.Tân An'];
        for ($i=0; $i <3; $i++) { 
            $random_keys=array_rand($branchListName);
            $attributesDep = [
                'name' => $branchListName[$random_keys],
                'address' => 'Long An',
                'shop_id' => $shop_id,
                'note' => $this->request->get('note'),
            ];
            $branch = $this->branchRepository->create($attributesDep);
            $branchList[]=$branch;
        }
        //Fake dep
        $depListName = [ 'IT ' ,'HR','OJI','SE','Marketing','Accounting','Human Resource','Financial','Pulic Relations','Training','Sales'];

        for ($i=0; $i <4 ; $i++) { 
            $random_keys=array_rand($depListName);
            $random_keys1=array_rand($branchList);
            $branch_id = $branchList[$random_keys1]["_id"];
            $attributes = [
                'name' => $depListName[$random_keys],
                'branch_id' => $branch_id,
                'shop_id' => $shop_id,
                'note' => $this->request->get('note')
            ];
            $dep = $this->depRepository->create($attributes);
            $depList[]= $dep;
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


        $timekeeConfigCheck = TimekeepConfig::where(['wifi' => $wifi,'location'=>$location])->first();


        if (!empty($timekeeConfigCheck)) {
            return $this->errorBadRequest('Timekeep_config đã được sử dụng');
        }
        $attributes = [
            'name' => $timekeep_name,
            'wifi' =>$wifi,
            'location' =>$location,
            'imageRequire' =>$timekeep_imageRequire,
            'shop_id' =>$shop_id
        ];

        $timekeepConfig = $this->timekeepConfigRepository->create($attributes);
        $timekeepConfigList[]=$timekeepConfig;
        //Fake user
        $userListName= ['Gia Bảo','Tô Bảo','Hoàng Ca','Mai Chung','Đỗ Cường','Thái Dương','Ngọc Đại','Hồng Đạo','Tiến Đạt','Hồng Điệp','Văn Đức',
        'Hữu Đức','Hoàng Giang','Trường Giảng','Nhật Hào','Chí Hải','Văn Hải','Đình Hậu','Thái Hòa','Tấn Huy','Phú Huy','Đăng Huy',
        'Huy Hùng','Ngọc Hưng','Đức Khang','Tường Khải','Cơ Khánh','Toàn Khoa','Đăng Khoa','Đình Khôi','Trung Kiên','Thanh Lâm','Hải Long',
        'Tuyên Long','Quang Minh','Phuong Nam','Trọng Ngôn','Kiều Oanh','Trần Phú','Minh Phú','Đăng Quang','Nhật Quang','Quang Quyền','Đình Sơn',
        'Phúc Sơn','Thiện Tâm','Hồng Thái','Huy Thắng','Văn Thắng','Hoàng Thi','Hưng Thịnh','Minh Thu','Thị Thư','Trung Thường','Hưng Tiến','Quang Tịnh','Văn Triều',
        'Minh Triết','Đức Trọng','Minh Trí','Toàn Trung','Trung Trường','Anh Tuấn','Quang Tùng','Minh Tú','Minh Vũ','Lê Vương','Chí Hòa','Thu Mai'];
        for ($i=0; $i <count($userListName,COUNT_NORMAL) ; $i++) { 
            $random_keys=array_rand($depList);
            $dep_id = $depList[$random_keys]["_id"];
            $branch_id =$depList[$random_keys]["branch_id"];
            $basic_salary = rand(5000000,14000000);
            $userAttributes = [
                'name' => $userListName[$i],
                'avatar' => 'http://192.168.1.3:8081/uploads/TanHuy.jpg',
                'email' => 'admin@gmail.com',
                'position_id' => null,
                'branch_id' => $branch_id,
                'dep_id' => $dep_id,
                'is_root' => 1,
                'phone_number' => (string)($i+1),
                'timekeep_config' =>$timekeepConfigList[0],
                'basic_salary' => $basic_salary,
                'shop_id' => $shop_id,
                'sex' => '1',
                'birth' => '1999-11-07',
            ];
            $user = $this->userRepository->create($userAttributes);
            $userList[]=$user;
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
            $random_keys=array_rand($depList);
            $dep_id = $depList[$random_keys]["_id"];
            $random_keys1=array_rand($branchList);
            $branch_id = $branchList[$random_keys1]["_id"];
            $attributes = [
                'name' => $shiftListName[$i],
                'shop_id' => $shop_id,
                'branch_ids' => $branch_id,
                'dep_ids' => $dep_id,
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
                    $user_id = $user['_id'];
                    $user_name =$user['name'];
                    $emp_shift = Empshift::where('user_id','=',$user_id)->where('shift_id','=',$shift_id)->where('working_date','<=',$day)->get();
                    //Các biến random
                    $late_check_in = rand(1,900);
                    $soon_check_out = rand(1,900);
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
                        'timekeep_config' => $timekeepConfigList[0],
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
                    $user_id = $user['_id'];
                    $user_name =$user['name'];
                    $emp_shift = Empshift::where('user_id','=',$user_id)->where('shift_id','=',$shift_id1)->where('working_date','<=',$day)->get();
                    //Các biến random
                    $late_check_in = rand(1,900);
                    $soon_check_out = rand(1,900);
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
                        'timekeep_config' => $timekeepConfigList[0],
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
       
    }//end Fake data

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
        $depCheck = Dep::where(['name' => $depname, 'branch_id' => mongo_id($branchCheck->_id)])->first();

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
