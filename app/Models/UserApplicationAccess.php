<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property string $access_id
 */
class UserApplicationAccess extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_application_access';

    /**
     * @var array
     */
    protected $fillable = [];

}
