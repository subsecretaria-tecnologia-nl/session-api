<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Entidad extends Model
{
    
    protected $table = 'estados';

    protected $fillable = ['clave', 'nombre'];

}
