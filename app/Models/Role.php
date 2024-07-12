<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    //para eliminado logico
    use SoftDeletes;

    //Nombre de la tabla
    protected $table = 'roles';

    //Llave primaria
    protected $primaryKey = 'idRole';

    //Campos de la tabla
    protected $fillable = [
        'name'
    ];

    //relacionar PK idRole con FK de users
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
