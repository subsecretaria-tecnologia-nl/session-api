<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $super_admin_id
 * @property string $user_id
 */
class UserRelationships extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_relationships';

    /**
     * @var array
     */
		protected $fillable = ["super_admin_id", "user_id"];
		


}
