<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 9/5/2018
 * Time: 12:09 PM
 */

namespace Vtv\Base\Services;

use Illuminate\Support\Facades\Mail;

class SendMailServices
{
    public static function send($to, $subject, $params, $layout)
    {
        $mailTo = array();
        // If {$to} is of Array type ex. [1,"abc@abc.com"]
        if (is_array($to)) {
            foreach ($to as $key => $t) {
                array_push($mailTo, $t); // If {$to} has any email string
            }
        } else {
            //ex. $to=1 or $to="abc@abc.com";
            array_push($mailTo, $to); // If {$to} is of single email string
        }
        $to = $mailTo;
        // Try Catch to handle any runtime exception
        try {
            Mail::send($layout, $params, function($message) use ($to, $subject) {
                $message->to($to);
                $message->subject($subject);
            });
            return 1;
        } catch (Exception $ex) {
            return $ex;
        }
    }
}