<?php

namespace App\Notifications;

use App\Api\Repositories\Contracts\NotificationRepository;
use App\Api\Entities\Notification;
use App\Api\Entities\User;
trait NotifiableTrait
{
    /**
     * @var UserRepository
     */
    protected $notificationRepository;

    /**
    * Onesignal URL
    *
    **/
    protected $_onesignalUrl = 'https://onesignal.com/api/v1/notifications/';

    public function __construct(NotificationRepository $notificationRepository) {
        $this->notificationRepository = $notificationRepository;
    }

    /**
    * @Description: Send notification to database.
    * @param String content: notification's content
    * @param Array params: list para when client access detail data.
    * @param Array users: List user will recieve notification.
    * @param String thumbnail: Url thumbnail
    **/
    // in your notification
    public function toDatabase($params)
    {
        $attributes = [
            'content' => $params['content'],
            'data' => $params['data'],
            'users'   => $params['users'],
        ];

        if(!empty($params['params'])){
            $attributes['params'] = $params['params'];
        }
        $notification = Notification::create($attributes);
        return $notification->_id;
    }
    /**
    * @param mixed $notifiable
    * @return NotificationChannels\OneSignal\OneSignalChannel
    **/
    public function toOneSignal($params)
    {
        //Không có nội dung thì return false ngay.
        if(empty($params['content']))
        {
          return false;
        }

        //Set limit time và limit memory.
        set_time_limit(0);
        ini_set("memory_limit",-1);

        $arrPush = [];

        // Set content cho notification.
        $arrPush['contents'] = ['en' => $params['content']]; 

        //Nếu có headings thì add headings vào
        if(!empty($params['heading'])) {
           $arrPush['headings'] = ['en' => $params['heading']]; 
        }

        if(!empty($params['data'])) {
            $arrPush['data'] = $params['data'];
        } 
        // $arrPush['data'] = ['id' => $params['noti_id'],
        //                     'activity' => $params['activity'],
        //                     'thumbnail' => $params['thumbnail']
        //                     ];
        //Set sound for app
        $arrPush['ios_sound'] = 'notification.mp3';
        $arrPush['android_sound'] = 'notification';

        $arrPush['android_visibility'] = 1;
        //Hẹn giờ để gửi tin nhắn
        if (!empty($params['send_after'])) {
            $data['send_after'] = $params['send_after'];
        }

        if(count($params['users'])>=1)
        {
            foreach($params['users'] as $key => $value)
            {
              $arrPush['user_id'] = strval($value);
              $arrPush['ios_badgeType'] = "SetTo";
              $arrPush['ios_badgeCount'] = 1; //Total noti
              //var_dump($arrPush);return;
              self::singlePushNotification($arrPush);
            }
            
        }else{
            $arrPush['option']['ios_badgeType'] = "Increase";
            $arrPush['option']['ios_badgeCount'] = 1;
            $arrPush['option']['included_segments'] = array('All');
            self::singlePushNotification($arrPush);
        }
    }

    /**
    * Push notificaiton sử dụng Onesignal
    * @param content: nội dung 
    */
    public function singlePushNotification($arrParam)
    {
        //Onesignal Config
        $onesignalConfig = config('services.onesignal');

        //Get current user's app
        $packageName = 'vn.greenapp.worktrack';
        $userInfo = User::where(['_id' => $arrParam['user_id']])->first();
        if(!empty($userInfo)) {
            if(!empty($userInfo->appInfo)) {
                $appInfo = $userInfo->appInfo;
                if(!empty($appInfo['package_name'])) {
                    
                    $packageName = $appInfo['package_name'];
                }
            }
        }
        $rest_api_key = $onesignalConfig[$packageName]['apiKey'];
        $arrInfoPush = array();
        $arrInfoPush['app_id'] = $onesignalConfig[$packageName]['appId'];
        if(!empty($arrParam))
        {
          $arrInfoPush = array_merge($arrInfoPush,$arrParam);
        }
        //var_dump($arrInfoPush);return;
        if($arrParam['user_id'])
        {
          $arrInfoPush['tags'][] = array('key'=>'userId','relation'=>'=','value'=>$arrParam['user_id']);
        }
        //unset($arrInfoPush['data']);
        $arrInfoPush = json_encode($arrInfoPush);
        //echo $arrInfoPush,'<br>';return;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_onesignalUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                               "Authorization: Basic {$rest_api_key}"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrInfoPush);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response,1);
        // echo "<pre>";
        // var_dump($response);
        return $response;
    }
}
