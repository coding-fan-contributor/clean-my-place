<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    private $email, $subject, $sender_email, $sender_name;
    public function __construct(){
        //
    }
    public function mailer($type, $email, $subject, $data=[]) {
        $this->email = $email;
        $this->subject = $subject;
        if($type){
            switch($type){
                case 'verification':
                    Mail::send('mail.verification', $data, function($message) {
                        $message->to($this->email)->subject($this->subject);
                        //$message->from('xyz@gmail.com','XYZ sender');
                    });
                break;
                case 'forgot':
                    Mail::send('mail.forgot', $data, function($message) {
                        $message->to($this->email)->subject($this->subject);
                        //$message->from('xyz@gmail.com','XYZ sender');
                    });
                break;
                case 'registration':
                    Mail::send('mail.registration', $data, function($message) {
                        $message->to($this->email)->subject($this->subject);
                        //$message->from('xyz@gmail.com','XYZ sender');
                    });
                break;
                case 'ordercreated':
                    Mail::send('mail.ordercreated', $data, function($message) {
                        $message->to($this->email)->subject($this->subject);
                    });
                break;
                
                default:
                    Mail::send('mail.default', $data, function($message) {
                        $message->to($this->email)->subject($this->subject);
                    });

            }
            return true;
        }else{
            return false;
        }
    }

    public function contact($email, $subject, $data=[], $sender_email, $sender_name){
        $this->email = $email;
        $this->subject = $subject;
        $this->sender_email = $sender_email;
        $this->sender_name = $sender_name;
        Mail::send('mail.contact', $data, function($message) {
            $message->to($this->email)->replyTo($this->sender_email, $this->sender_name)->subject($this->subject);
        });
        return true;
    }
}
