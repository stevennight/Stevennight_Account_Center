<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model;

class EmailVerifyToken extends Model{

    protected $table = 'email_verify_token';
    protected $primaryKey ='id';

    public $timestamps = true;
    protected function getDateFormat(){
        return time();
    }

    protected function  asDateTime($value)
    {
        return $value;
    }
}