<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 */
class CatalogUserType extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'catalog_token_types';

    /**
     * @var array
     */
    protected $fillable = ['name', 'description'];

}