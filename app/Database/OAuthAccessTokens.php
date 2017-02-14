<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model;

class OAuthAccessTokens extends Model{

    protected $table = 'oauth_access_tokens';
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