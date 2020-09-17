<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserHistory extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_history';

    /**
     * @var array
     */
    protected $fillable = [
			'comment', 
			'old_data', 
			'new_data', 
			'table_name', 
			'change_id', 
			'created_by', 
			'created_at' , 
			'updated_at'
		];

}
