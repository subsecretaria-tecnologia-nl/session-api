<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $comment
 * @property string $old_data
 * @property string $new_data
 * @property int $created_by
 * @property string $created_at
 */
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
    protected $fillable = ['comment', 'old_data', 'new_data', 'created_by', 'created_at'];

}
