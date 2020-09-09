<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property int $token_type_id
 * @property int $created_by
 * @property string $valid_until
 * @property string $closed_at
 * @property string $created_at
 * @property string $updated_at
 */
class UserToken extends Model
{
    /**
     * @var array
     */
		protected $fillable = [
			'user_id', 
			'token', 
			'token_type_id', 
			'created_by', 
			'valid_until', 
			'closed_at', 
			'created_at', 
			'updated_at'
		];

		public function scopeSessionsAct($query)
		{
				return $query->where('closed_at', null);
		}
}
