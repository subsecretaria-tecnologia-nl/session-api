<?php

namespace App\Models;

use App\Observers\HistoryObserver;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject, CanResetPasswordContract
{
	use Authenticatable, Authorizable, HistoryObserver, Notifiable, CanResetPassword;
		public $function;
		protected $table ="users";
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

		protected $hidden = ['password'];

		protected $appends = ['function'];


		public function getJWTIdentifier()
		{
				return $this->getKey();
		}
		public function getJWTCustomClaims()
		{
				return [];
		}
		public function permission(){
			return $this->belongsToMany('App\Models\CatalogUserAction', 'App\Models\UserPermission', 'user_id', 'action_id');
		}
		public function roles(){
			return $this->hasOne('App\Models\CatalogUserRoles', 'id', 'role_id');
		}
		
		public function subusers(){
			return $this->hasMany('App\Models\UserRelationships', 'super_admin_id', 'id');
		}

		public function tokens(){
			return $this->hasMany('App\Models\UserToken', 'user_id', 'id');
		}
	

	


	
	

}
