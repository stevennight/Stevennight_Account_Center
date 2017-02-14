<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model;

class OAuthAuthCodes extends Model{

    protected $table = 'oauth_auth_codes';
    protected $primaryKey ='id';

    public $timestamps = false;
}