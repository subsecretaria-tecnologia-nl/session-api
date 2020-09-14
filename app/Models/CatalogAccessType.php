<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 */
class CatalogAccessType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'description'];

}
