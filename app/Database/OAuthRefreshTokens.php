<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model;

class OAuthRefreshTokens extends Model{

    protected $table = 'oauth_refresh_tokens';
    protected $primaryKey ='id';

    public $timestamps = false;
}