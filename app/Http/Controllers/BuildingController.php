<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    //recuperar un registro
    public function findBuilding($id)
    {
        try {
            // Buscar el registro de edificio por ID
            $registro = Building::find($id);

            // Si el registro no existe, retornar un error 404
            if (!$registro) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Edificio no encontrado'
                ], 404);
            }

            // Retornar una respuesta JSON con el registro encontrado
            return response()->json([
                'code' => 200,
                'message' => 'Edificio encontrado',
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


    //recuperar todos los registros
    public function selectBuildings()
    {
        try {
            // Recuperar todos los registros de la tabla buildings
            $data = Building::all();

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
    public function storeBuilding(Request $request)
    {
        try {
            // Validar los datos recibidos en la petición
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'address' => 'required',
                'description' => 'sometimes' // 'sometimes' significa que el campo es opcional
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
            $registro = Building::withTrashed()->where('name', $request->name)->first();

            if ($registro) {
                if ($registro->trashed()) {
                    // Si el registro está eliminado lógicamente, restaurarlo
                    $registro->restore();
                    return response()->json([
                        'code' => 200,
                        'message' => 'Edificio restaurado con éxito',
                        'data' => $registro
                    ], 200);
                } else {
                    // Si el registro existe y no está eliminado, retornar un error de validación
                    return response()->json([
                        'code' => 400,
                        'message' => 'Error de validación',
                        'errors' => ['name' => ['El nombre del edificio ya existe']]
                    ], 400);
                }
            }

            // Si el registro no existe, insertar el nuevo registro
            $nuevoRegistro = Building::create($request->all());

            // Retornar una respuesta JSON indicando éxito
            return response()->json([
                'code' => 200,
                'message' => 'Edificio insertado correctamente',
                'data' => $nuevoRegistro
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


    //actualizar un registro
    public function updateBuilding(Request $request, $id)
    {
        try {
            // Validar los datos recibidos en el request
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'address' => 'required',
                'description' => 'sometimes' // 'sometimes' significa que el campo es opcional
            ]);

            // Si la validación falla, retornar mensajes de error
            if ($validacion->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Error de validación',
                    'errors' => $validacion->messages()
                ], 400);
            }

            // Buscar el registro de edificio por ID
            $registro = Building::find($id);

            // Si el registro no existe, retornar un error 404
            if (!$registro) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Edificio no encontrado'
                ], 404);
            }

            // Verificar si el nuevo nombre ya existe en otro registro
            $otroRegistro = Building::withTrashed()->where('name', $request->name)->where('idBuilding', '!=', $id)->first();
            if ($otroRegistro) {
                return response()->json([
                    'code' => 400,
                    'message' => 'El nombre del edificio ya existe',
                    'errors' => ['name' => ['El nombre del edificio ya existe']]
                ], 400);
            }

            // Actualizar el registro con los datos recibidos
            $registro->update([
                'name' => $request->name,
                'address' => $request->address,
                'description' => $request->description
            ]);

            // Retornar una respuesta JSON indicando éxito
            return response()->json([
                'code' => 200,
                'message' => 'Edificio actualizado correctamente',
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


    //eliminar un registro
    public function deleteBuilding($id)
    {
        try {
            // Se busca el edificio por ID
            $data = Building::find($id);

            if ($data) {
                // Si el edificio existe, se elimina
                $data->delete();

                // Se retorna una respuesta en formato JSON indicando éxito
                return response()->json([
                    'code' => 200,
                    'message' => 'Edificio eliminado correctamente'
                ], 200);
            } else {
                // Si no se encuentra el edificio, se retorna una respuesta en formato JSON indicando que no se encontró
                return response()->json([
                    'code' => 404,
                    'message' => 'Edificio no encontrado'
                ], 404);
            }
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            return response()->json([
                'code' => 500,
                'message' => 'Ha ocurrido un error al intentar eliminar el edificio',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
