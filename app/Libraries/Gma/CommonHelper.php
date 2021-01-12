<?php

namespace Gma;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class CommonHelper
{
    public static function getSlug($name)
    {
        $slug = build_slug($name);

        return $slug;
    }
    /**
    * $delay: Send after: $delay minutes
    */
    public static function sendMail($email, Mailable $mail, $delay = 0)
    {
        if (empty($email) || !$mail) {
            return;
        }

        $emailDebug = env('EMAIL_DEBUG', false);
        $emailDebug = boolval($emailDebug);

        if ($emailDebug) {
            $email = env('EMAIL_DEBUG_SENT', $email);
        }

        if ($emailDebug) {
            Mail::to($email)->send($mail);
        } else {
            if($delay>0)  {
                return Mail::to($email)->later($delay*60, $mail->onQueue('emails'));
            } else {
                // return Mail::to($email)->queue($mail->onQueue('emails'));
                return Mail::to($email)->send($mail);
            }
        }
    }

    public static function dispatchJob(ShouldQueue $job, $options = ['force_process' => false])
    {
        $queueDebug = env('QUEUE_DEBUG', false);
        $queueDebug = boolval($queueDebug);
        $forceProcess = false;
        if(!empty($options['force_process'])) {
            $forceProcess = $options['force_process'];
        }
        if ($queueDebug || $forceProcess) {
            $job->handle();
        } else {
            if(!empty($options['queue_name'])) {
                $queueName = $options['queue_name'];
                $objId = dispatch($job->onQueue($queueName));
            } else {
                $objId = dispatch($job);
            }
        }
        return $objId;
    }
}
