<?php

namespace App\Api\Repositories\Contracts;

use Carbon\Carbon;
use App\Api\Entities\EmpClock;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EmpClockRepository
 */
interface EmpClockRepository extends RepositoryInterface
{
    public function createSalary(Empclock $empclock);
    public function checkRangeTime(Carbon $time_in,Carbon $time_out,Carbon $time_shift_in,Carbon $time_shift_out);
}
