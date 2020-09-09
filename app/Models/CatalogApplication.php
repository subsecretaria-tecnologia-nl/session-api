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
class CatalogApplication extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'catalog_application';

    /**
     * @var array
     */
    protected $fillable = ['name', 'description', 'url', 'access_type_id'];

}
