<?php

namespace App;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements
    AuthenticatableContract,
		AuthorizableContract,
		JWTSubject
{
    use Authenticatable, Authorizable;


    protected $fillable = [
        'name', 'email', 'password', 'api_token'
    ];

   
    protected $hidden = [
        'password', 'remember_token'
		];

		
		public function getJWTIdentifier()
		{
				return $this->getKey();
		}
		public function getJWTCustomClaims()
		{
				return [];
		}

		public function sesiones()
    {
        return $this->hasMany('App\Session');
    }
		
}
