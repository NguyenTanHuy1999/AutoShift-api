<?php

namespace App\Notifications;

class Notification
{
    /**
    * @var $sendToOnesignal
    **/
    protected $clsNotification = '';

    public  static function send($clsNotification){
        $clsNotification->send();
    }
}
