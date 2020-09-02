<?php


namespace App;
use App\Session;

use Illuminate\Database\Eloquent\Model;

class subUser extends Model
{
    protected $table = 'sub_users';

    protected $fillable = [
			'user_id',
			'id_user_created_by'

    ];

		protected $hidden = ['created_at', 'updated_at'];

		public function sessions()
    {
			return $this->hasMany('App\Session', 'user_id', 'user_id');
		}

	
}