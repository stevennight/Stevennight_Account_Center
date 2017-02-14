<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model;

class ResetPasswordToken extends Model{

    protected $table = 'reset_password_token';
    protected $primaryKey ='id';

    public $timestamps = false;
    protected function getDateFormat(){
        return time();
    }

    protected function  asDateTime($value)
    {
        return $value;
    }
}