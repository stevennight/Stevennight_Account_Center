<?php
namespace  App\Mail;

use App\Database\ConfigGlobalWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailVerifyTokenMail extends Mailable{

    use Queueable,SerializesModels;

    protected $mailval;

    public function __construct($mailval){

        $this->mailval = $mailval;

    }

    public function build(){

        return $this->view('emails.template.token',[
                    'userid' => $this->mailval['userid'],
                    'username' => $this->mailval['username'],
                    'email' => $this->mailval['email'],
                    'token' => $this->mailval['token'],
                ])
            ->from(ConfigGlobalWebsite::all()->first()->email)
            ->subject(str_replace(':website', ConfigGlobalWebsite::all()->first()->name,trans('view.email.verify.email_verify_token_template_title')));

    }

}