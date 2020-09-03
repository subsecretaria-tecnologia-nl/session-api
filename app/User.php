<?php

namespace App;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements
    AuthenticatableContract,
		AuthorizableContract,
		JWTSubject,
		CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;


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
        return $this->hasMany('App\Session');
		}

		public function subusers()
    {
				return $this->hasMany('App\SubUser', 'id_user_created_by', 'id');
		
    }



	
		
		
}
