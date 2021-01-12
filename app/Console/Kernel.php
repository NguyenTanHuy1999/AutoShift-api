<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Schedules\ConfirmShiftWeek;
use App\Schedules\UpdateTimeKeeperToEmployeeShift;
use App\Schedules\AutoCheckinCheckoutShift;
use App\Schedules\PostFbFormLeadToBitrix;
use App\Schedules\MakeScheduleSystemNotification;
use App\Schedules\ExcuteScheduleSystemNotification;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
    }
}
