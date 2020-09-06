<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $table = 'information_users';

    protected $fillable = [
			'user_id',					
			'action_date',
			'token_type',
			'description',
			'modified_variables',
			'device_type',
			'browser_type'

    ];

		protected $hidden = ['created_at', 'updated_at'];

}