<?php

namespace App\Models;
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
        'id', 'name', 'email', 'password', 'api_token', 'rol_id'
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

		public function sessions()
    {
        return $this->hasMany('App\Models\Session');
		}

		public function subusers()
    {
				return $this->hasMany('App\Models\SubUser', 'id_user_created_by', 'id');
		
    }



	
		
		
}
