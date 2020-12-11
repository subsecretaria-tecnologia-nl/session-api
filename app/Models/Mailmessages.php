<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $url
 * @property int $access_type_id
 */
class Mailmessages extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mail_messages';

    /**
     * @var array
     */
    protected $fillable = ['user', 'password', 'message', 'sent'];

}
