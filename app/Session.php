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
			'type_platform'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}