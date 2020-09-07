<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $token_id
 * @property int $quantity
 * @property string $created_at
 * @property string $updated_at
 */
class UserTokenSession extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['token_id', 'quantity', 'created_at', 'updated_at'];

}
