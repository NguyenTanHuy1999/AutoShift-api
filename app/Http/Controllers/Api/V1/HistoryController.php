<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Api\Entities\Shift;
use App\Api\Repositories\Contracts\EmpshiftRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\EmpClockRepository;
use App\Api\Repositories\Contracts\HistoryRepository;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\User;
use App\Api\Entities\Empshift;
use App\Api\Entities\EmpClock;
use App\Api\Entities\History;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class HistoryController extends Controller
{
    protected $empshiftRepository;
    protected  $userRepository;
    protected $empclockRepository;
    protected $historyRepository;

    protected $auth;

    protected $request;
    public function __construct(
        AuthManager $auth,
        Request $request,
        HistoryRepository $historyRepository
    ) {
        $this->request = $request;
        $this->auth = $auth;
        $this->historyRepository = $historyRepository;
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



    public function list()
    {
        $user = $this->user();
        $emp_history = History::where(['user_id' => mongo_id($user->_id)])->orderBy('created_at', 'desc')->get();
        $emp_his = [];
        foreach ($emp_history as $index) {
            $emp_his[] = $index->transform();
        }

        return $this->successRequest($emp_his);
    }
}
