<?php

namespace App\Api\Repositories\Eloquent;

use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\EmpClockRepository;
use App\Api\Entities\EmpClock;
use App\Api\Validators\EmpClockValidator;

/**
 * Class EmpClockRepositoryEloquent
 */
class EmpClockRepositoryEloquent extends BaseRepository implements EmpClockRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return EmpClock::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
    public function createSalary(EmpClock $empClock)
    {
        $empclockTrans=collect($empClock->transform('for-calculating-salary'));
        $time_in=$empclockTrans['time_in'];
        $time_out=$empclockTrans['time_out'];
        $user_id=$empclockTrans['user_id'];
        $shift_id=$empclockTrans['shift_id'];
        $time_shift_in=$empclockTrans['shift']['time_begin'];
        $time_shift_out=$empclockTrans['shift']['time_end'];
        $work_sals = [];
        $time_shift_in=Carbon::createFromTime((int)$time_shift_in);
        $time_shift_out=Carbon::createFromTime((int)$time_shift_out);
        $work_time=$this->checkRangeTime($time_in,$time_out,$time_shift_in,$time_shift_out);
        $attribute=[
            'user_id'=>$user_id,
            'work_time'=>$work_time,
        ];
        return $attribute;
    }
    public function checkRangeTime(Carbon $time_in,Carbon $time_out,Carbon $time_shift_in,Carbon $time_shift_out)
    {
        //A và B là thời gian vào và thời gian ra của ca đó
        // A' và B' là thời gian checkin và checkout
        //              A---------------B
        //    A'-------
        // TH1 : A' nằm ngoài A
        if($time_in<($time_shift_in))
        {
            //B' cũng nằm ngoài A
            //           A-----B
            // A'-----B'
            if($time_out<=$time_shift_in)
            {
                return 0;
            }
            //B' nằm trong đoạn A---------B
            //              A'-------B'
            if($time_out>$time_shift_in && $time_out<=$time_shift_out)
            {
                return ($time_shift_in->diffInSeconds($time_out))/3600;
            }
            //B' nằm ngoài đoạn A---B về phía B'
            //  A------B
            //A'----------B'
            if($time_out>$time_shift_out)
            {
                return ($time_shift_out->diffInSeconds($time_shift_in))/3600;
            }
        }
        //TH 2: A' nằm trong đoạn A--------B
        //                          A'----
        if ($time_in>=$time_shift_in && $time_in <=$time_shift_out)
        {
            //B' nằm trong đoạn A---------------B
            //                   A'-----B'
            if($time_out<=$time_shift_out)
            {
                return ($time_out->diffInSeconds($time_in))/3600;
            }
            //B' nằm ngoài đoạn A-------------B
            //                    A'-------------B'
            if($time_out>$time_shift_out)
            {
                return ($time_shift_out->diffInSeconds($time_in))/3600;
            }
        }
        //TH 3 : A' nằm ngoài đoạn A-------B
        //                                   A'-------B'
        if($time_in>$time_shift_out)
        {
            return 0;
        }
    }
}
