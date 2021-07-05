<?php


namespace App\Http\Controllers\Api\V1;


use Carbon\Carbon;
use App\Api\Repositories\Contracts\EmpClockRepository;
use App\Api\Repositories\Contracts\SalaryRepository;
use App\Api\Repositories\Contracts\HistoryRepository;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\EmpClock;
use App\Api\Entities\Empshift;
use App\Api\Entities\Shift;
use App\Api\Entities\WifiConfig;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class EmpClockController extends Controller
{
    protected $empshiftRepository;

    protected $userRepository;
    protected $empclockRepository;
    protected $salaryRepository;
    protected $historyRepository;
    protected $shiftRepository;

    protected $auth;

    protected $request;
    public function __construct(
        AuthManager $auth,
        Request $request,
        EmpClockRepository $empClockRepository,
        SalaryRepository $salaryRepository,
        HistoryRepository $historyRepository
    ) {
        $this->request = $request;
        $this->auth = $auth;
        $this->empclockRepository = $empClockRepository;
        $this->salaryRepository = $salaryRepository;
        $this->historyRepository = $historyRepository;
        parent::__construct();
    }


    public function preClock()
    {
        $user = $this->user();
        $user_id = $user->id;
        $branch_id = $user->branch_id;
        $dep_id = $user->dep_id;
        $timekeep_config_client = $this->request->get('timekeep_config');
        //Lấy ca của user trong ngày
        $from  = Carbon::now()
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->toDateTime(); // 2018-09-29 00:00:00

        $to    = Carbon::now()
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->toDateTime(); // 2018-09-29 23:59:59
        //bỏ đoạn này
       // $wifi_query = [];

        //if (!empty($branch_id)) {
            //$wifi_query['branch_id'] = $branch_id;
        //}

        //if (!empty($dep_id)) {
       //     $wifi_query['dep_id'] = $dep_id;
        //}

        //$wifi_config = WifiConfig::where($wifi_query)->get();
        //$wifi_clocking = [];
        //if (!empty($wifi_config)) {
        //    foreach ($wifi_config as $wifi) {

        //        $wifi_clocking[] = $wifi->transform();
       //     }
       // }
        
       
       //lấy thông tin timekeep_config của user
        $user_timekeepConfig = $user->timekeep_config;
        $user_timekeepConfig_wifi = $user_timekeepConfig['wifi'];
        $user_long = $user_timekeepConfig['location']['long'];
        $user_lat = $user_timekeepConfig['location']['lat'];

        //thông tin timekeep_config từ client trả về 
        $timekeep_config_client_wifi = $timekeep_config_client['wifi'];
        $timekeep_config_client_long = $timekeep_config_client['location']['long'];
        $timekeep_config_client_lat = $timekeep_config_client['location']['lat'];

        //xử lý validate timekeep_config
        if($timekeep_config_client_wifi != $user_timekeepConfig_wifi){
            return $this->errorBadRequest('Wifi kết nối không phù hợp');
        }
        //khoảng cách từ điểm cố định đến điểm client trả về
        $distance = sqrt(($timekeep_config_client_long*$timekeep_config_client_long - 2*$timekeep_config_client_long*$user_long + $user_long*$user_long) + 
                        ($timekeep_config_client_lat*$timekeep_config_client_lat - 2*$timekeep_config_client_lat*$user_lat + $user_lat*$user_lat));

        if($distance >0.001){
            return $this->errorBadRequest('Bạn đang ở quá xa vị trí chấm công');
        }
        
        $timekeep_config_clocking[] = $timekeep_config_client;
        // dd($from, $to);
        $emp_shifts = Empshift::whereBetween('working_date', [$from, $to])
            ->where(['user_id' => $user_id])
            ->get();

        $data = [];
        foreach ($emp_shifts as $emp_shift) {

            $data[] = $emp_shift->transform();
        }
        return $this->successRequest(['listShift' => $data, 'timekeepConfigClock' => $timekeep_config_clocking]);
    }





    public function  clock()
    {
        //        Log::debug('test0');
        $user = $this->user();
        $user_id = $user->id;
        $emp_shift_id = $this->request->get('shift_id');
        $timekeep_config = $this->request->get('timekeep_config');


        //Lấy thời gian lúc nhân viên bấm
        $now = Carbon::now();

        //Nếu nhân viên chưa chấm công
        $status = 0;


        //Vào ca
        if (!empty($emp_shift_id)) {
            //Lấy ca để chấm
            $emp_shift = Empshift::where(['_id' => mongo_id($emp_shift_id)])->first();

            //lay thong tin ca lam

            $shift_check = null;
            $shift_id = null;
            $working_date = null;
            $shift_name = null;
            $shift_time = null;

            if (!empty($emp_shift)) {
                $shift_check = $emp_shift->shift();
                $shift_id = $shift_check->_id;
                $working_date = $emp_shift->working_date;
                $shift_name = $shift_check->name;
                $shift_time = ($shift_check->time_begin) . '-' . ($shift_check->time_end);
            }
            $status = 1;
            //data history
            $data = [
                'user_id' => mongo_id($user->_id),
                'user_name' => $user->name,
                'working_date' => $working_date,
                'emp_shift_id' => mongo_id($emp_shift_id),
                'shift_name' => $shift_name,
                'shift_time' => $shift_time,
                'shift_id' => mongo_id($shift_id),
                'time_check' => $now,
                'status' => $status,
                'timekeep_config' => $timekeep_config,
                'type' => 'check_in'
            ];
            $emp_history = $this->historyRepository->create($data);


        
            $attribute = [
                'user_id' => mongo_id($user->_id),
                'shift_id' => mongo_id($shift_id),
                'emp_shift_id' => mongo_id($emp_shift_id),
                'time_in' => $now,
                'time_out' => null,
                'status' => $status,
                'isCheckOut' => false,
            ];

            $emp_clock = $this->empclockRepository->create($attribute);
            return $this->successRequest($emp_clock->transform());
        } else {
            //ham ktra xem da vao ca hay chua
            $clock_check =
                EmpClock::where([
                    'user_id' => mongo_id($user->_id),
                    'isCheckOut' => false
                ])
                ->first();


            //lay thong tin ca lam
            $emp_shift_id = $clock_check->emp_shift_id;
            $shift_check = null;
            $shift_id = null;
            $working_date = null;
            $shift_name = null;
            $shift_time = null;
            $time_begin = null;
            $time_end =null;
             //Lấy ca để chấm        
            $emp_shift = Empshift::where(['_id' => mongo_id($emp_shift_id)])->first();



            if (!empty($emp_shift_id)) {
                $shift_check = $emp_shift->shift();
                $shift_id = $shift_check->_id;
                $working_date = $emp_shift->working_date;
                $shift_name = $shift_check->name;
                $time_begin = $shift_check->time_begin;
                $time_end = $shift_check->time_end;
                $shift_time = ($shift_check->time_begin) . '-' . ($shift_check->time_end);
            }


            //Ra ca 
            $attribute = [
                'time_out' => $now,
                'status' => $status,
                'isCheckOut' => true,
            ];
            $emp_clock = $this->empclockRepository->update($attribute, $clock_check->_id);
            //tính giờ công
            $time_in = $clock_check->time_in;
            $emp_time_in = Carbon::parse($time_in);
            $time_out = $clock_check->time_out;
            $emp_time_out = Carbon::parse($time_out);
            $work_time = $emp_time_out->diffInSeconds($emp_time_in);
            //late check in
            $shift_begin = Carbon::parse($time_begin);
            $late_check_in = $emp_time_in->diffInSeconds($shift_begin);
            //soon check out
            $shift_end = Carbon::parse($time_end);
            $soon_check_out =$shift_end->diffInSeconds($emp_time_out);
            //month
            $month = Carbon::now()->month;
            //year
            $year = Carbon::now()->year;
            //data history
            $data = [
                'user_id' => mongo_id($user->_id),
                'user_name' => $user->name,
                'working_date' => $working_date,
                'emp_shift_id' => mongo_id($emp_shift_id),
                'shift_id' => mongo_id($shift_id),
                'shift_name' => $shift_name,
                'shift_time' => $shift_time,
                'time_check' => $now,
                'status' => $status,
                'type' => 'check_out',
                'real_working_hours'=>$work_time,
                'late_check_in' =>$late_check_in,
                'soon_check_out' =>$soon_check_out,
                'month' =>$month,
                'year' =>$year,
            ];
            $emp_clock = $this->empclockRepository->update($attribute, $clock_check->_id);
            $emp_history = $this->historyRepository->create($data);
           
            return $this->successRequest($emp_clock->transform());
        }
    }
}
