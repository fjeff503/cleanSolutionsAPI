<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Level;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    //recuperar un registro de Level
    public function findLevel($id)
    {
        try {
            // Buscar el registro de nivel por ID, incluyendo los eliminados lógicamente
            $registro = Level::find($id);

            // Si el registro no existe, retornar un error 404
            if (!$registro) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Nivel no encontrado'
                ], 404);
            }

            // Retornar una respuesta JSON con el registro encontrado
            return response()->json([
                'code' => 200,
                'message' => 'Nivel encontrado',
                'data' => $registro
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            return response()->json([
                'code' => 500,
                'message' => 'Ha ocurrido un error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Recuperar niveles por edificio
    public function selectLevelsForBuilding($idBuilding)
    {
        try {
            // Recuperar todos los niveles del edificio específico
            $levels = Level::where('idBuilding', $idBuilding)->get();

            // Verificar si hay registros
            if ($levels->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'No hay registros de niveles para este edificio'
                ], 404);
            }

            // Si hay registros, devolverlos con un código 200
            return response()->json([
                'code' => 200,
                'message' => 'Registros de niveles recuperados correctamente',
                'data' => $levels
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la consulta
            return response()->json([
                'code' => 500,
                'message' => 'Error al recuperar los datos de niveles: ' . $e->getMessage()
            ], 500);
        }
    }

    //recuperar todos los registros
    public function selectLevels()
    {
        try {
            // Recuperar todos los registros de la tabla levels
            $data = Level::all();

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

    //almacenar un registro
    public function storeLevel(Request $request)
    {
        try {
            // Validar los datos recibidos en la petición
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'sometimes', // opcional
                'idBuilding' => 'required|integer|exists:buildings,idBuilding' // verificar existencia en buildings
            ]);

            // Si la validación falla, retornar mensajes de error
            if ($validacion->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Error de validación',
                    'errors' => $validacion->messages()
                ], 400);
            }

            // Verificar si el registro existe en la base de datos, incluyendo los eliminados lógicamente
            $registro = Level::withTrashed()
                ->where('name', $request->name)
                ->where('idBuilding', $request->idBuilding)
                ->first();

            if ($registro) {
                if ($registro->trashed()) {
                    // Si el registro está eliminado lógicamente, restaurarlo
                    $registro->restore();
                    return response()->json([
                        'code' => 200,
                        'message' => 'Nivel restaurado con éxito',
                        'data' => $registro
                    ], 200);
                } else {
                    // Si el registro existe y no está eliminado, retornar un error de validación
                    return response()->json([
                        'code' => 400,
                        'message' => 'Error de validación',
                        'errors' => ['name' => ['El nivel ya existe']]
                    ], 400);
                }
            }

            // Si el registro no existe, insertar el nuevo registro
            $nuevoRegistro = Level::create($request->all());

            // Retornar una respuesta JSON indicando éxito
            return response()->json([
                'code' => 200,
                'message' => 'Nivel insertado correctamente',
                'data' => $nuevoRegistro
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Ha ocurrido un error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Actualizar un registro de Level
    public function updateLevel(Request $request, $id)
    {
        try {
            // Validar los datos recibidos en el request
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'sometimes', // opcional
                'idBuilding' => 'required|integer|exists:buildings,idBuilding' // verificar existencia en buildings
            ]);

            // Si la validación falla, retornar mensajes de error
            if ($validacion->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Error de validación',
                    'errors' => $validacion->messages()
                ], 400);
            }

            // Buscar el registro de nivel por ID
            $registro = Level::find($id);

            // Si el registro no existe, retornar un error 404
            if (!$registro) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Nivel no encontrado'
                ], 404);
            }

            // Verificar si el nuevo nombre ya existe en otro registro de Level para el mismo idBuilding
            $otroRegistro = Level::withTrashed()
                ->where('name', $request->name)
                ->where('idBuilding', $request->idBuilding)
                ->where('idLevel', '!=', $id)
                ->first();

            if ($otroRegistro) {
                return response()->json([
                    'code' => 400,
                    'message' => 'El nombre del nivel ya existe para este edificio',
                    'errors' => ['name' => ['El nombre del nivel ya existe para este edificio']]
                ], 400);
            }

            // Actualizar el registro con los datos recibidos
            $registro->update([
                'name' => $request->name,
                'description' => $request->description,
                'idBuilding' => $request->idBuilding
            ]);

            // Retornar una respuesta JSON indicando éxito
            return response()->json([
                'code' => 200,
                'message' => 'Nivel actualizado correctamente',
                'data' => $registro
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            return response()->json([
                'code' => 500,
                'message' => 'Ha ocurrido un error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar un registro de Level
    public function deleteLevel($id)
    {
        try {
            // Buscar el nivel por ID
            $nivel = Level::find($id);

            if ($nivel) {
                // Si el nivel existe, se elimina
                $nivel->delete();

                // Retornar una respuesta JSON indicando éxito
                return response()->json([
                    'code' => 200,
                    'message' => 'Nivel eliminado correctamente'
                ], 200);
            } else {
                // Si no se encuentra el nivel, retornar un error 404
                return response()->json([
                    'code' => 404,
                    'message' => 'Nivel no encontrado'
                ], 404);
            }
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            return response()->json([
                'code' => 500,
                'message' => 'Ha ocurrido un error al intentar eliminar el nivel',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
