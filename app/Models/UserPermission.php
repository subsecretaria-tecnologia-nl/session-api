<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property string $action_id
 */
class UserPermission extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_action_permission';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'action_id'];

}
