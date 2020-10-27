<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 */
class CatalogUserRoles extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'catalog_user_roles';

    /**
     * @var array
     */
    protected $fillable = ['id','name', 'description'];

    public function scopeNotaryRole($query, $tipo) {
        if ($tipo) {
            return $query->where('name','like',"%notary_$tipo%");
        }
    
    
    }
    public function scopeNombreRol($query, $id){
        if ($id) {
            return $query->select('name')->where('id', $id);
        }
    }

}
