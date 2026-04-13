<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigurationRequest;
use App\Http\Resources\ConfigurationResource;
use App\Models\Configuration;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    use ApiResponse;

    // Obtener configuración del usuario autenticado
    public function show(Request $request)
    {
        $config = Configuration::where('user_id', $request->user()->id)->first();
        if (!$config) {
            return $this->errorResponse('Configuración no encontrada', 404);
        }
        return $this->successResponse(
            new ConfigurationResource($config),
            'Configuración obtenida exitosamente',
            200
        );
    }

    // Crear o actualizar configuración del usuario autenticado
    public function update(ConfigurationRequest $request)
    {
        $config = Configuration::updateOrCreate(
            ['user_id' => $request->user()->id],
            // user_id se agrega explícitamente por si es un registro nuevo
            array_merge($request->validated(), ['user_id' => $request->user()->id])
        );
        $config->load('user');
        return $this->successResponse(
            new ConfigurationResource($config),
            'Configuración actualizada exitosamente',
            200
        );
    }
}
