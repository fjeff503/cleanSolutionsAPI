<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use HasFactory;

    //para eliminado logico
    use SoftDeletes;

    //Nombre de la tabla
    protected $table = 'levels';

    //Llave primaria
    protected $primaryKey = 'idLevel';

    //Campos de la tabla
    protected $fillable = [
        'name',
        'description',
        'idBuilding'
    ];

    //relacionar PK idLevel con FK de rooms
    public function room()
    {
        return $this->hasMany(Room::class);
    }

    //relacionar FK idBuilding con PK de buildings
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
