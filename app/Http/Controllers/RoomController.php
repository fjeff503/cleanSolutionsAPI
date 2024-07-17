<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    // Recuperar un registro de room por ID
    public function findRoom($id)
    {
        try {
            // Buscar el registro de habitación por ID, incluyendo los eliminados lógicamente si está usando SoftDeletes
            $room = Room::withTrashed()->find($id);

            // Si el registro no existe, retornar un error 404
            if (!$room) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Estancia no encontrada'
                ], 404);
            }

            // Retornar una respuesta JSON con el registro encontrado
            return response()->json([
                'code' => 200,
                'message' => 'Estancia encontrada',
                'data' => $room
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

    // Recuperar Rooms por Level
    public function selectRoomsForLevel($idLevel)
    {
        try {
            // Recuperar todos los estancias del nivel específico
            $rooms = Room::where('idLevel', $idLevel)->get();

            // Verificar si hay registros
            if ($rooms->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'No hay registros de estancias para este nivel o este nivel no existe'
                ], 404);
            }

            // Si hay registros, devolverlos con un código 200
            return response()->json([
                'code' => 200,
                'message' => 'Registros de estancias recuperados correctamente',
                'data' => $rooms
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la consulta
            return response()->json([
                'code' => 500,
                'message' => 'Error al recuperar los datos de estancias: ' . $e->getMessage()
            ], 500);
        }
    }

    // Recuperar todos los registros de room
    public function selectRooms()
    {
        try {
            // Recuperar todos los registros de la tabla rooms
            $rooms = Room::all();

            // Verificar si hay registros
            if ($rooms->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'No hay registros de Estancias'
                ], 404);
            }

            // Si hay registros, devolverlos con un código 200
            return response()->json([
                'code' => 200,
                'message' => 'Registros de habitaciones recuperados correctamente',
                'data' => $rooms
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la consulta
            return response()->json([
                'code' => 500,
                'message' => 'Error al recuperar los datos de estancias: ' . $e->getMessage()
            ], 500);
        }
    }

    //almacenar un registro
    public function storeRoom(Request $request)
    {
        try {
            // Validar los datos recibidos en la petición
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'sometimes', // opcional
                'lastCleaning' => 'sometimes', // opcional
                'idLevel' => 'required|integer|exists:levels,idLevel' // Verificar existencia en niveles
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
            $registro = Room::withTrashed()
                ->where('name', $request->name)
                ->where('idLevel', $request->idLevel)
                ->first();

            if ($registro) {
                if ($registro->trashed()) {
                    // Si el registro está eliminado lógicamente, restaurarlo
                    $registro->restore();
                    return response()->json([
                        'code' => 200,
                        'message' => 'Estancia restaurada con éxito',
                        'data' => $registro
                    ], 200);
                } else {
                    // Si el registro existe y no está eliminado, retornar un error de validación
                    return response()->json([
                        'code' => 400,
                        'message' => 'Error de validación',
                        'errors' => ['name' => ['La estancia ya existe']]
                    ], 400);
                }
            }

            // Si el registro no existe, insertar el nuevo registro
            $nuevoRegistro = Room::create($request->all());

            // Retornar una respuesta JSON indicando éxito
            return response()->json([
                'code' => 200,
                'message' => 'Estancia insertada correctamente',
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

    // Actualizar un registro de Room
    public function updateRoom(Request $request, $id)
    {
        try {
            // Validar los datos recibidos en la petición
            $validacion = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'sometimes', // opcional
                'lastCleaning' => 'sometimes', // opcional
                'idLevel' => 'required|integer|exists:levels,idLevel' // Verificar existencia en niveles
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
            $registro = Room::find($id);

            // Si el registro no existe, retornar un error 404
            if (!$registro) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Estancia no encontrada'
                ], 404);
            }

            // Verificar si el nuevo nombre ya existe en otro registro de Level para el mismo idBuilding
            $otroRegistro = Room::withTrashed()
                ->where('name', $request->name)
                ->where('idLevel', $request->idLevel)
                ->where('idRoom', '!=', $id)
                ->first();

            if ($otroRegistro) {
                return response()->json([
                    'code' => 400,
                    'message' => 'El nombre de la estancia ya existe para este nivel',
                    'errors' => ['name' => ['El nombre de la estancia ya existe para este nivel']]
                ], 400);
            }

            // Actualizar el registro con los datos recibidos
            $registro->update([
                'name' => $request->name,
                'description' => $request->description,
                'lastCleaning' => $request->lastCleaning,
                'idLevel' => $request->idLevel
            ]);

            // Retornar una respuesta JSON indicando éxito
            return response()->json([
                'code' => 200,
                'message' => 'Estancia actualizada correctamente',
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

    // Eliminar un registro de Room
    public function deleteRoom($id)
    {
        try {
            // Buscar la Estancia por ID
            $room = Room::find($id);

            if ($room) {
                // Si la Estancia existe, se elimina
                $room->delete();

                // Retornar una respuesta JSON indicando éxito
                return response()->json([
                    'code' => 200,
                    'message' => 'Estancia eliminada correctamente'
                ], 200);
            } else {
                // Si no se encuentra la Estancia, retornar un error 404
                return response()->json([
                    'code' => 404,
                    'message' => 'Estancia no encontrada'
                ], 404);
            }
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            return response()->json([
                'code' => 500,
                'message' => 'Ha ocurrido un error al intentar eliminar la habitación',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
