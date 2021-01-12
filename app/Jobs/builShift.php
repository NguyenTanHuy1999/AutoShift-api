<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class builShift extends Job
{
    /**
     * Create a new job instance.
     */
    public function __construct(){
        
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::debug('Begin Job');

        //Todo here
        Log::debug('End Job');

        return true;
    }
}
