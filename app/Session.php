<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
			'user_id',					
			'payload',
			'login_datetime',
			'logout_datetime',
			'token_type',
			'session_lifetime',
			'device_type',
			'browser_type'

    ];

		protected $hidden = ['created_at', 'updated_at'];

		public function scopeSessionsAct($query)
		{
				return $query->where('logout_datetime', null);
		}
}