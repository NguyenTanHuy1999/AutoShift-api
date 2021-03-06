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
    protected $positionRepository;

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
        PositionRepository $positionRepository,
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
        $this->positionRepository = $positionRepository;
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
        $positionList =[];
        //$branchList =[];
        $shiftList =[];
        $empShiftList =[];
        $wifiList = [];
         //Fake shop 
         // T???o shop tr?????c
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
            'phone_number' => (string)1,
            //'timekeep_config' =>$timekeepConfigList,
            'basic_salary' =>10000000,
            'shop_id' => $shop_id,
            'sex' => 1,
            'birth' => '1999-11-07',
        ];
        $user = $this->userRepository->create($admin_info);
        $users = $user->transform();
        $userList[]=$users;
        
        //Fake branch b??? branch
       // $branchListName = [ 'C???n ???????c' ,'C???n Giu???c','B???n L???c','???????c H??a','M???c H??a','Th???nh H??a','T??n Tr???','Th??? Th???a','TP.T??n An'];
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
        //    return $this->errorBadRequest('Timekeep_config ???? ???????c s??? d???ng');
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
        $userListName= ['Gia B???o','T?? B???o','Ho??ng Ca','Mai Chung','????? C?????ng','Th??i D????ng','Ng???c ?????i','H???ng ?????o','Ti???n ?????t','H???ng ??i???p','V??n ?????c',
        'H???u ?????c','Ho??ng Giang','Tr?????ng Gi???ng','Nh???t H??o','Ch?? H???i','V??n H???i','????nh H???u','Th??i H??a','Ph?? Huy','????ng Huy',
        'Huy H??ng','Ng???c H??ng','?????c Khang','T?????ng Kh???i','C?? Kh??nh','To??n Khoa','????ng Khoa','????nh Kh??i','Trung Ki??n','Thanh L??m','H???i Long',
        'Tuy??n Long','Quang Minh','Phuong Nam','Tr???ng Ng??n','Ki???u Oanh','Tr???n Ph??','Minh Ph??','????ng Quang','Nh???t Quang','Quang Quy???n','????nh S??n',
        'Ph??c S??n','Thi???n T??m','H???ng Th??i','Huy Th???ng','V??n Th???ng','Ho??ng Thi','H??ng Th???nh','Minh Thu','Th??? Th??','Trung Th?????ng','H??ng Ti???n','Quang T???nh','V??n Tri???u',
        'Minh Tri???t','?????c Tr???ng','Minh Tr??','To??n Trung','Trung Tr?????ng','Anh Tu???n','Quang T??ng','Minh T??','Minh V??','L?? V????ng','Ch?? H??a','Thu Mai'];
        for ($i=0; $i <count($userListName,COUNT_NORMAL) ; $i++) { 
            $random_keys=array_rand($depList);
            $dep_id = $depList[$random_keys]["_id"];
            $positionList_id =$positionList[$random_keys]["_id"];
            $basic_salary = rand(5000000,14000000);
            $userAttributes = [
                'name' => $userListName[$i],
                'avatar' => 'http://192.168.1.3:8081/uploads/TanHuy.jpg',
                'email' => 'admin@gmail.com',
                'position_id' => $positionList_id,
                //'branch_id' => $branch_id,
                'dep_id' => $dep_id,
                'is_root' => 0,
                'is_admin' =>rand(0,1),
                'phone_number' => (string)($i+2),
                //'timekeep_config' =>$timekeepConfigList,
                'basic_salary' => $basic_salary,
                'shop_id' => $shop_id,
                'sex' => 1,
                'birth' => '1999-11-07',
            ];
            $user = $this->userRepository->create($userAttributes);
            $users = $user->transform();
            $userList[]=$users;
        }
        //Fake Shift
        // T???o ca l???n
        $shiftListName =['Ca S??ng','Ca Chi???u'];
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
            //T???o ca cho t???ng nh??n vi??n
            //T???o ca trong 1 n??m
            
            $work_date_begin = Carbon::now()->startofYear();
            $work_date_end = Carbon::now()->endOfYear();
            //Kho???ng th???I gian kh???i t???o ca
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
        //Fake Wifi b??? ph???n n??y
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



        //Fake EmpClock ca s??ng
        //shift_id ca s??ng
        $shift_id = $shiftList[0]["_id"];
        $shift_name = $shiftList[0]["name"];
        $work_date_begin = Carbon::now()->startofYear();
        $work_date_end = Carbon::now();
        //Kho???ng th???I gian kh???i t???o ca
        $work_date = CarbonPeriod::create($work_date_begin, $work_date_end);
        foreach ($userList as $user) {
            foreach ($work_date as $day) {
                $dayOfWeek = $day->dayOfWeek;
                if ($assignments[$dayOfWeek]) {
                    $time_check = $day->addHour(7)->addMinute(55)->addMinutes(rand(1,20));
                    $user_id = $user['id'];
                    $user_name =$user['name'];
                    $emp_shift = Empshift::where('user_id','=',$user_id)->where('shift_id','=',$shift_id)->where('working_date','<=',$day)->get();
                    //C??c bi???n random
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

        //Fake EmpClock ca chi???u
        //shift_id Ca chi???u
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
                    //C??c bi???n random
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
        return $this->successRequest('Fake d??? li???u th??nh c??ng!!');
       
    }//end Fake data

    #region tao phong ban
    public function registerDep()
    {
        $user = $this->user();
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            //'branch_id' => 'required',
            'name' => 'required',
            'note' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $depname = $this->request->get('name');
        //$branchCheck = Branch::where(['_id' => mongo_id($this->request->get('branch_id')),])->first();
        $depCheck = Dep::where(['name' => $depname,'shop_id'=> mongo_id($user->shop_id)])->first();

        // dd($depCheck->name);
        //if (empty($branchCheck)) {
        //    return $this->errorBadRequest(trans('Chi nh??nh kh??ng t???n t???i'));
        //} else {
        if (!empty($depCheck)) {
            return $this->errorBadRequest(trans('Ph??ng ban ???? t???n t???i'));
            
        }
        //}

        $attributes = [
            'name' => $depname,
            //'branch_id' => mongo_id($branchCheck->_id),
            'shop_id' => $user->shop_id,
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
        // Ki???m tra xem email ???? ???????c ????ng k?? tr?????c ???? ch??a
        $idCheck = Dep::where(['_id' => $id])->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Ph??ng ban kh??ng t???n t???i'));
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
        // Ki???m tra xem email ???? ???????c ????ng k?? tr?????c ???? ch??a

        $idCheck = Dep::where(['_id' => $id])->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Ph??ng ban kh??ng t???n t???i'));
        }


        // T???o shop tr?????c
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
    #region sua ph??ng ban
    public function updateDep()
    {
        // Validate Data import.
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }

        $id = $this->request->get('id');
        // Ki???m tra xem id ???? ???????c ????ng k?? tr?????c ???? ch??a

        $idCheck = $this->depRepository->find(mongo_id($id))->first();
        if (empty($idCheck)) {
            return $this->errorBadRequest(trans('Ph??ng ban kh??ng t???n t???i'));
        }
        // l???y th??ng tin ????? s???a
        $attributes = [
            'name' => $this->request->get('name'),
        ];
        $dep = $this->depRepository->update($attributes, mongo_id($id));
        return $this->successRequest($dep->transform());

        // return $this->successRequest($user->transform());
    }
    #endregion
}
