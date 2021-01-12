<?php

namespace App\Api\Repositories\Eloquent;

use App\Api\Entities\Role;
use App\Api\Entities\User;
use App\Api\Repositories\Contracts\RoleRepository;
use App\Api\Repositories\Contracts\UserRepository;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Gma\CommonHelper;


use App\Api\Criteria\UserCriteria;

use App\Mails\AlertAdmin;
use App\Mails\WelcomeUsingAccountDemo;
use App\Mails\ActiveAccount;
use App\Mails\IntroAutomaticHrm;
use App\Mails\RemindCustomer;
use App\Mails\Trial30Days;
use App\Mails\HowToUse;
use App\Mails\FromThanhNhan;

/**
 * Class UserRepositoryEloquent.
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    protected $roleRepository;

    protected $organizationRepository;

    public function __construct(RoleRepository $roleRepository, 
                                Application $app){
        $this->roleRepository = $roleRepository;

        parent::__construct($app);
    }
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        //$this->pushCriteria(app(RequestCriteria::class));
    }
    public function getListUser($params = [], $limit = 0) {
        $this->pushCriteria(new UserCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();  
        } else {
            $item = $this->all(); 
        } 
        $this->popCriteria(new UserCriteria($params));
        return $item;
    }
    /**
    * Get room User of shop
    **/
    public function getRootUser($params = [], $limit = 0) {
        $params['is_root'] = 1;
        $params['is_detail'] = 1;
        $params['shop_id'] = Auth::getPayload()->get('shop_id');
        $organization = $this->organizationRepository->getOrganization($params);
        if(empty($organization)) {
            return [];
        }
        $params = [];
        $params['user_id'] = $organization->user_id;
        $this->pushCriteria(new UserCriteria($params));
        $item = $this->get()->first(); 
        $this->popCriteria(new UserCriteria($params));
        return $item;
    }


    /**
     * Send mail welcome User.
     **/
    public function sendMailAlertAdmin($params)
    {
        $mail = new AlertAdmin($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }

    /**
     * Send mail welcome user and using acount tancademo.
     **/
    public function sendMailUsingAccountDemo($params)
    {
        $mail = new WelcomeUsingAccountDemo($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }

    /**
     * Send mail acitive account demo
     **/
    public function sendMailActiveAccount($params)
    {
        $mail = new ActiveAccount($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }

    /**
     * Send mail intro automatic Hrm
     **/
    public function sendMailAutomaticHrm($params)
    {
        $mail = new IntroAutomaticHrm($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }
    /**
     * Send mail remind customer
     **/
    public function sendMailRemindCustomer($params)
    {
        $mail = new RemindCustomer($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }

    /**
     * Send mail trial 30 days
     **/
    public function sendMailTrial30Days($params)
    {
        $mail = new Trial30Days($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }
    /**
    * Send mail how to use
    **/
    public function sendMailHowToUse($params)
    {
        $mail = new HowToUse($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }
    /**
    * Send mail from thanh nhan
    **/
    public function sendMailFromThanhNhan($params)
    {
        $mail = new FromThanhNhan($params);
        $delay =0;
        if(!empty($params['delay'])) {
            $delay = (int)$params['delay'];
        }
        CommonHelper::sendMail($params['sendTo'], $mail, $delay);
    }
}
