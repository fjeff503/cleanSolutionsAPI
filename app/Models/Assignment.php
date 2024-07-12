<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory;

    //para eliminado logico
    use SoftDeletes;

    //Nombre de la tabla
    protected $table = 'assignments';

    //Llave primaria
    protected $primaryKey = 'idAssignment';

    //Campos de la tabla
    protected $fillable = [
        'dateAssignment',
        'state',
        'description',
        'idUser',
        'idRoom'
    ];

    //relacionar FK idUser con PK de users
    public function user()
    {
        return $this->belongsTo(User::class);
    } 

    //relacionar FK idRoom con PK de rooms
    public function room()
    {
        return $this->belongsTo(Room::class);
    } 
}
