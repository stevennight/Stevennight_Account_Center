<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model;

class OAuthClients extends Model{

    protected $table = 'oauth_clients';
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