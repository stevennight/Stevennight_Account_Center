<?php
namespace  App\Mail;

use App\Database\ConfigGlobalWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailResetPasswordToken extends Mailable{

    use Queueable,SerializesModels;

    protected $mailval;

    public function __construct($mailval){

        $this->mailval = $mailval;

    }

    public function build(){

        return $this->view('emails.template.resetPasswordToken',$this->mailval)
            ->from(ConfigGlobalWebsite::all()->first()->email)
            ->subject(str_replace(':website', ConfigGlobalWebsite::all()->first()->name,trans('view.resetPassword.reset_email_title')));

    }

}