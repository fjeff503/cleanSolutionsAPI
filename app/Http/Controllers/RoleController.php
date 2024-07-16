<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
        //recuperar todos los registros
        public function selectRoles() 
        {
            try {
                // Recuperar todos los registros de la tabla roles
                $data = Role::all();
        
                // Verificar si hay registros
                if ($data->isEmpty()) {
                    return response()->json([
                        'code' => 404,
                        'data' => 'No hay registros'
                    ], 404);
                }
        
                // Si hay registros, devolverlos con un código 200
                return response()->json([
                    'code' => 200,
                    'data' => $data
                ], 200);
        
            } catch (\Exception $e) {
                // Manejar cualquier excepción que ocurra durante la consulta
                return response()->json([
                    'code' => 500,
                    'message' => 'Error al recuperar los datos: ' . $e->getMessage()
                ], 500);
            }
        }
}
