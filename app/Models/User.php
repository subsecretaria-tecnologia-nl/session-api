<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
	use Authenticatable, Authorizable;
    /**
     * @var array
     */
    protected $fillable = [
			'username', 
			'email', 
			'password', 
			'role_id', 
			'name', 
			'mothers_surname', 
			'fathers_surname', 
			'curp', 
			'rfc', 
			'phone', 
			'status', 
			'created_by', 
			'created_at', 
			'updated_at', 
			'deleted_at'
		];

		protected $hidden = [
			'password'
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

		public function roles()
    {
        return $this->hasOne('App\Models\Roles', 'id', 'role_id');
		}

		public function subsusuarios(){
			return $this->hasMany('App\Models\Relationships', 'super_admin_id', 'id');
		}

		public function permission(){
			return $this->hasMany('App\Models\UserPermission', 'user_id', 'id');
		}
		public function tokens(){
			return $this->hasMany('App\Models\UserToken', 'user_id', 'id');
		}

		

	


}
