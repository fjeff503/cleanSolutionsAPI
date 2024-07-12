<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use HasFactory;
    
    //para eliminado logico
    use SoftDeletes;

    //Nombre de la tabla
    protected $table = 'buildings';

    //Llave primaria
    protected $primaryKey = 'idBuilding';

    //Campos de la tabla
    protected $fillable = [
        'name',
        'address',
        'description'
    ];

    //relacionar PK idBuilding con FK de levels
    public function levels()
    {
        return $this->hasMany(Level::class);
    }
    
}
