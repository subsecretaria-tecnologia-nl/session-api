<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $parametro
 * @property string $descripcion
 */
class Divisa extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'descripcion', 'parametro'];

}
