<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;

    //para eliminado logico
    use SoftDeletes;

    //Nombre de la tabla
    protected $table = 'rooms';

    //Llave primaria
    protected $primaryKey = 'idRoom';

    //Campos de la tabla
    protected $fillable = [
        'name',
        'description',
        'lastCleaning',
        'idLevel'
    ];

    //relacionar PK idRoom con FK de assignments
    public function room()
    {
        return $this->hasMany(Assignment::class);
    }

    //relacionar FK idLevel con PK de levels
    public function level()
    {
        return $this->belongsTo(Level::class);
    }    
    
}
